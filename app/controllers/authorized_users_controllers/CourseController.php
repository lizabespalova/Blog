<?php

namespace controllers\authorized_users_controllers;

use models\Courses;
require_once 'app/services/helpers/session_check.php';

class CourseController
{
    private $courseModel;
    public function __construct($conn) {
        $this->courseModel = new Courses($conn);

    }
    public function showUserCourses($username) {
        require_once 'app/services/helpers/switch_language.php';
        require_once 'app/views/courses/my_courses.php';
    }
}