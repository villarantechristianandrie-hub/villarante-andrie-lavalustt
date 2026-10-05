<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * ApiController - JSON API for the Product Management System.
 *
 *  POST   /api/register        create an account
 *  POST   /api/login           returns access + refresh token
 *  POST   /api/refresh         exchange refresh token for new tokens
 *  POST   /api/logout          revoke refresh token
 *  GET    /api/me              current user          (JWT)
 *  GET    /api/products        list                  (JWT)
 *  GET    /api/products/{id}   one product           (JWT)
 *  POST   /api/products        create                (JWT)
 *  PUT    /api/products/{id}   update (PATCH too)    (JWT)
 *  DELETE /api/products/{id}   delete                (JWT)
 */
class ApiController extends Controller
{
    public function before_action()
    {
        // Loading the library sends the CORS headers and answers OPTIONS.
        $this->call->library('api');
        $this->call->database();
        $this->call->model('UsersModel');
        $this->call->model('ProductModel');
    }

    /** OPTIONS preflight - the Api library already replied with 204. */
    public function preflight()
    {
        $this->api->respond(null, 204);
    }

    // ------------------------------------------------------------
    // Authentication
    // ------------------------------------------------------------
    public function register()
    {
        $this->api->require_method('POST');
        $this->api->rate_limit('register:' . ($_SERVER['REMOTE_ADDR'] ?? 'x'), 10, 60);

        $in       = $this->input();
        $username = $in['username'] ?? '';
        $email    = $in['email'] ?? '';
        $password = $in['password'] ?? '';

        if ($username === '' || $email === '' || $password === '') {
            $this->api->respond_error('username, email and password are required.', 422);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->api->respond_error('Email is not valid.', 422);
        }
        if (strlen($password) < 6) {
            $this->api->respond_error('Password must be at least 6 characters.', 422);
        }
        if ($this->UsersModel->find_by('username', $username)) {
            $this->api->respond_error('That username is already taken.', 409);
        }
        if ($this->UsersModel->find_by('email', $email)) {
            $this->api->respond_error('That email is already registered.', 409);
        }

        $id = $this->UsersModel->insert([
            'username'  => $username,
            'email'     => $email,
            'password'  => password_hash($password, PASSWORD_DEFAULT),
            'role'      => 'user',
            'is_active' => 1,
        ]);

        $this->api->respond(['message' => 'Account created.', 'id' => (int) $id], 201);
    }

    public function login()
    {
        $this->api->require_method('POST');
        $this->api->rate_limit('login:' . ($_SERVER['REMOTE_ADDR'] ?? 'x'), 10, 60);

        $in       = $this->input();
        $username = $in['username'] ?? '';
        $password = $in['password'] ?? '';

        if ($username === '' || $password === '') {
            $this->api->respond_error('username and password are required.', 422);
        }

        $user = $this->UsersModel->find_by('username', $username);

        if (!$user || !password_verify($password, $user['password'])) {
            $this->api->respond_error('Invalid username or password.', 401);
        }
        if (isset($user['is_active']) && !$user['is_active']) {
            $this->api->respond_error('This account has been deactivated.', 403);
        }

        $tokens = $this->api->issue_tokens([
            'id'     => (int) $user['id'],
            'role'   => $user['role'],
            'scopes' => ['read', 'write'],
        ]);

        $this->api->respond([
            'message' => 'Login successful.',
            'tokens'  => $tokens,
            'user'    => $this->public_user($user),
        ]);
    }

    public function refresh()
    {
        $this->api->require_method('POST');
        $in = $this->input();

        if (empty($in['refresh_token'])) {
            $this->api->respond_error('refresh_token is required.', 422);
        }
        $this->api->refresh_access_token($in['refresh_token']); // responds + exits
    }

    public function logout()
    {
        $this->api->require_method('POST');
        $in = $this->input();

        if (!empty($in['refresh_token'])) {
            $this->api->revoke_refresh_token($in['refresh_token']);
        }
        $this->api->respond(['message' => 'Logged out.']);
    }

    public function me()
    {
        $this->api->require_method('GET');
        $auth = $this->api->require_jwt();

        $user = $this->UsersModel->find((int) $auth['sub']);
        if (!$user) {
            $this->api->respond_error('User not found.', 404);
        }
        $this->api->respond(['user' => $this->public_user($user)]);
    }

    // ------------------------------------------------------------
    // Products (all endpoints require a valid JWT)
    // ------------------------------------------------------------
    public function products_index()
    {
        $this->api->require_method('GET');
        $this->api->require_jwt();

        $rows = $this->ProductModel->all();
        $this->api->respond([
            'data'  => array_map([$this, 'format_product'], $rows ?: []),
            'count' => count($rows ?: []),
        ]);
    }

    public function products_show($id)
    {
        $this->api->require_method('GET');
        $this->api->require_jwt();

        $this->api->respond(['data' => $this->format_product($this->find_or_404($id))]);
    }

    public function products_store()
    {
        $this->api->require_method('POST');
        $this->require_admin();

        [$data, $error] = $this->validate_product($this->input(), false);
        if ($error) {
            $this->api->respond_error($error, 422);
        }

        $new_id  = $this->ProductModel->insert($data);
        $product = $this->find_or_404($new_id);

        $this->api->respond([
            'message' => 'Product created.',
            'data'    => $this->format_product($product),
        ], 201);
    }

    public function products_update($id)
    {
        // Accept both PUT and PATCH
        if (!in_array($_SERVER['REQUEST_METHOD'], ['PUT', 'PATCH'], true)) {
            $this->api->respond_error('Method Not Allowed', 405);
        }
        $this->require_admin();
        $this->find_or_404($id);

        // PUT = full update, PATCH = partial update
        $partial = $_SERVER['REQUEST_METHOD'] === 'PATCH';
        [$data, $error] = $this->validate_product($this->input(), $partial);
        if ($error) {
            $this->api->respond_error($error, 422);
        }
        if (empty($data)) {
            $this->api->respond_error('Nothing to update.', 422);
        }

        $this->ProductModel->update((int) $id, $data);

        $this->api->respond([
            'message' => 'Product updated.',
            'data'    => $this->format_product($this->find_or_404($id)),
        ]);
    }

    public function products_delete($id)
    {
        $this->api->require_method('DELETE');
        $this->require_admin();
        $this->find_or_404($id);

        $this->ProductModel->delete((int) $id);
        $this->api->respond(['message' => 'Product deleted.']);
    }

    // ------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------

    private function require_admin(): array
    {
        $auth = $this->api->require_jwt();

        if (($auth['role'] ?? null) !== 'admin') {
            $this->api->respond_error('Administrator access required.', 403);
        }

        return $auth;
    }

    /**
     * Request body as an array. Api::body() HTML-escapes every string, which
     * would corrupt passwords and product text, so the escaping is undone
     * here - output is escaped by the frontend (React) when rendering.
     */
    private function input(): array
    {
        $body = $this->api->body();
        array_walk_recursive($body, function (&$v) {
            if (is_string($v)) {
                $v = htmlspecialchars_decode($v, ENT_QUOTES);
            }
        });
        return $body;
    }

    private function find_or_404($id)
    {
        $product = $this->ProductModel->find((int) $id);
        if (!$product) {
            $this->api->respond_error('Product not found.', 404);
        }
        return $product;
    }

    /**
     * @return array [array $data, string|null $error]
     */
    private function validate_product(array $in, bool $partial): array
    {
        $data = [];

        if (!$partial || array_key_exists('product_name', $in)) {
            $name = trim((string) ($in['product_name'] ?? ''));
            if ($name === '')           return [[], 'Product name is required.'];
            if (strlen($name) > 100)    return [[], 'Product name must be 100 characters or fewer.'];
            $data['product_name'] = $name;
        }

        if (!$partial || array_key_exists('description', $in)) {
            $data['description'] = trim((string) ($in['description'] ?? ''));
        }

        if (!$partial || array_key_exists('price', $in)) {
            $price = $in['price'] ?? '';
            if ($price === '' || !is_numeric($price) || (float) $price < 0) {
                return [[], 'Price must be a valid non-negative number.'];
            }
            $data['price'] = number_format((float) $price, 2, '.', '');
        }

        if (!$partial || array_key_exists('quantity', $in)) {
            $qty = $in['quantity'] ?? '';
            if ($qty === '' || !ctype_digit((string) $qty)) {
                return [[], 'Quantity must be a valid non-negative whole number.'];
            }
            $data['quantity'] = (int) $qty;
        }

        return [$data, null];
    }

    private function format_product(array $p): array
    {
        return [
            'id'           => (int) $p['id'],
            'product_name' => $p['product_name'],
            'description'  => $p['description'],
            'price'        => (float) $p['price'],
            'quantity'     => (int) $p['quantity'],
            'created_at'   => $p['created_at'],
        ];
    }

    private function public_user(array $u): array
    {
        return [
            'id'       => (int) $u['id'],
            'username' => $u['username'],
            'email'    => $u['email'],
            'role'     => $u['role'],
        ];
    }
}
