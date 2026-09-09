<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');


class StudentController extends Controller
{
    
    private $student = [
        'student_id'  => '0',
        'name'        => '.',
        'course'      => 'BSIT',
        'year'        => '3rd Year',
        'section'     => '3-3',
        'email'       => 'l.com',
        'address'     => 'a',
        'contact'     => '098190',
        'skills'      => 'PHP, Jesign',
        'bio'         => 'Kay dedicated student with a strong interest in web development and programming. I enjoy learning new technologies and applying them to real-world projects. In my free time, I like to explore new places, read tech blogs, and work on personal coding projects.',
    ];

  
    public function index()
    {
       
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['profile_access'] = true;

        $data['name'] = $this->student['name'];
        $data['denied'] = isset($_GET['denied']);

        $this->call->view('student_home', $data);
    }

    
    public function profile()
    {
        $this->call->view('student_profile', $this->student);
    }
}
