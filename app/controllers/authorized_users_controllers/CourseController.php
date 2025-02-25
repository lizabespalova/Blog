<?php

namespace controllers\authorized_users_controllers;

use models\Articles;
use models\Courses;
require_once 'app/services/helpers/session_check.php';

class CourseController
{
    private $courseModel;
    private $articleModel;

    public function __construct($conn) {
        $this->courseModel = new Courses($conn);
        $this->articleModel = new Articles($conn);
    }
    public function showUserCourses($username) {
        require_once 'app/services/helpers/switch_language.php';
        // Получаем список избранных статей с деталями
        $articles= $this->articleModel->getUserArticles($username);
        require_once 'app/views/courses/my_courses.php';
    }
}