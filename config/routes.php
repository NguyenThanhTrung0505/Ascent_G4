<?php
return [
    // Auth routes
    'auth.login'             => '/app/controllers/auth/login.php',
    'auth.register'          => '/app/controllers/auth/register.php',
    'auth.reset'             => '/app/controllers/auth/reset-password.php',
    'auth.logout'            => '/app/controllers/auth/logout.php',

    // Student routes
    'student.dashboard'      => '/app/controllers/student/Dashboard.php',
    'student.many-class'     => '/app/controllers/student/Many-class.php',
    'student.class'          => '/app/controllers/student/Class.php',
    'student.topic'          => '/app/controllers/student/Topic.php',
    'student.lesson'         => '/app/controllers/student/Lesson-detail.php',
    'student.take-exam'      => '/app/controllers/student/Take-exam.php',

    // Teacher routes
    'teacher.dashboard'      => '/app/controllers/teacher/Dashboard.php',
    'teacher.many-class'     => '/app/controllers/teacher/ManyClass.php',
    'teacher.class-detail'   => '/app/controllers/teacher/Class-detail.php',
    'teacher.chapter-detail' => '/app/controllers/teacher/Chapter-detail.php',
    'teacher.chapter-list' => '/app/controllers/teacher/Chapter-list.php',
    'teacher.exam-list' => '/app/controllers/teacher/Exam-list.php',
    'teacher.create-question' => '/app/controllers/teacher/Create-question.php',
    'teacher.update-question' => '/app/controllers/teacher/Update-question.php',
    'teacher.task-today'     => '/app/controllers/teacher/Task-today.php',

    // Admin routes
    'admin.dashboard'        => '/app/controllers/admin/Dashboard.php',
    'admin.users'            => '/app/controllers/admin/Users.php',
    'admin.exam'             => '/app/controllers/admin/Exam.php',
    'admin.class'               => '/app/controllers/admin/Class.php',

    // Home routes
    'home.index'             => '/app/controllers/home/index.php',
];
