<?php
/**
 * Routes - Définition de toutes les routes de l'application
 * Séparées par domaine fonctionnel
 */

$router = new Router();

// ========== PUBLIC ==========
$router->add('', 'PublicController', 'home');
$router->add('home', 'PublicController', 'home');
$router->add('a-propos', 'PublicController', 'about');
$router->add('services', 'PublicController', 'services');
$router->add('tarifs', 'PublicController', 'pricing');
$router->add('faq', 'PublicController', 'faq');
$router->add('contact', 'PublicController', 'contact');
$router->add('contact', 'PublicController', 'submitContact', ['csrf']);
$router->add('sitemap.xml', 'PublicController', 'sitemap');
$router->add('robots.txt', 'PublicController', 'robots');
$router->add('manifest.json', 'PublicController', 'manifest');

// ========== AUTH ==========
$router->add('connexion', 'AuthController', 'showLogin', ['guest']);
$router->add('connexion', 'AuthController', 'login', ['guest', 'csrf']);
$router->add('inscription', 'AuthController', 'showRegister', ['guest']);
$router->add('inscription', 'AuthController', 'register', ['guest', 'csrf']);
$router->add('verifier-email', 'AuthController', 'verifyEmail', ['guest']);
$router->add('mot-de-passe-oublie', 'AuthController', 'showForgotPassword', ['guest']);
$router->add('mot-de-passe-oublie', 'AuthController', 'forgotPassword', ['guest', 'csrf']);
$router->add('reinitialiser-mot-de-passe', 'AuthController', 'showResetPassword', ['guest']);
$router->add('reinitialiser-mot-de-passe', 'AuthController', 'resetPassword', ['guest', 'csrf']);
$router->add('deconnexion', 'AuthController', 'logout', ['auth']);
$router->add('auth/google/callback', 'AuthController', 'googleCallback', ['guest']);

// ========== STUDENT DASHBOARD ==========
$router->add('tableau-de-bord', 'StudentController', 'dashboard', ['auth']);
$router->add('profil', 'StudentController', 'profile', ['auth']);
$router->add('profil', 'StudentController', 'updateProfile', ['auth', 'csrf']);
$router->add('classement', 'StudentController', 'leaderboard', ['auth']);
$router->add('badges', 'StudentController', 'achievements', ['auth']);
$router->add('abonnements', 'StudentController', 'subscriptions', ['auth']);
$router->add('mes-cours', 'StudentController', 'myLessons', ['auth']);

// ========== LESSONS ==========
$router->add('matieres', 'LessonController', 'subjects', ['auth']);
$router->add('cours/{subjectSlug}', 'LessonController', 'lessons', ['auth']);
$router->add('lecon/{slug}', 'LessonController', 'show', ['auth']);
$router->add('lecon/progres-video', 'LessonController', 'updateVideoProgress', ['auth', 'csrf']);
$router->add('lecon/debloquer-xp', 'LessonController', 'unlockWithXp', ['auth', 'csrf']);
$router->add('recherche', 'LessonController', 'search', ['auth']);

// ========== QUIZZES ==========
$router->add('quiz/{lessonSlug}', 'QuizController', 'take', ['auth']);
$router->add('quiz/soumettre', 'QuizController', 'submit', ['auth', 'csrf']);
$router->add('quiz/resultat/{attemptId}', 'QuizController', 'result', ['auth']);

// ========== BLOG ==========
$router->add('blog', 'BlogController', 'index');
$router->add('blog/{slug}', 'BlogController', 'show');
$router->add('blog/categorie/{slug}', 'BlogController', 'category');
$router->add('blog/recherche', 'BlogController', 'search');

// ========== EVENTS ==========
$router->add('evenements', 'EventController', 'index');
$router->add('evenement/{slug}', 'EventController', 'show');
$router->add('evenement/inscription', 'EventController', 'register', ['auth', 'csrf']);

// ========== PAYMENT ==========
$router->add('checkout/{planSlug}', 'PaymentController', 'checkout', ['auth']);
$router->add('paiement/process', 'PaymentController', 'process', ['auth', 'csrf']);
$router->add('paiement/confirmation', 'PaymentController', 'confirmation', ['auth']);
$router->add('webhook/cmi', 'PaymentController', 'webhookCMI');
$router->add('webhook/paypal', 'PaymentController', 'webhookPayPal');

// ========== ADMIN ==========
$router->add('admin', 'AdminController', 'dashboard', ['admin']);
$router->add('admin/analytics', 'AdminController', 'analytics', ['admin']);

// Users
$router->add('admin/utilisateurs', 'AdminController', 'users', ['admin']);
$router->add('admin/utilisateurs/{id}', 'AdminController', 'editUser', ['admin']);
$router->add('admin/utilisateurs/modifier', 'AdminController', 'updateUser', ['admin', 'csrf']);

// Levels & Filieres
$router->add('admin/niveaux', 'AdminController', 'levels', ['admin']);
$router->add('admin/niveaux/creer', 'AdminController', 'storeLevel', ['admin', 'csrf']);
$router->add('admin/niveaux/modifier', 'AdminController', 'updateLevel', ['admin', 'csrf']);
$router->add('admin/filieres/creer', 'AdminController', 'storeFiliere', ['admin', 'csrf']);

// Subjects
$router->add('admin/matieres', 'AdminController', 'subjects', ['admin']);
$router->add('admin/matieres/creer', 'AdminController', 'storeSubject', ['admin', 'csrf']);

// Lessons
$router->add('admin/lecons', 'AdminController', 'lessons', ['admin']);
$router->add('admin/lecons/creer', 'AdminController', 'storeLesson', ['admin', 'csrf']);
$router->add('admin/lecons/modifier', 'AdminController', 'updateLesson', ['admin', 'csrf']);
$router->add('admin/lecons/supprimer', 'AdminController', 'deleteLesson', ['admin', 'csrf']);

// Quizzes
$router->add('admin/quiz', 'AdminController', 'quizzes', ['admin']);
$router->add('admin/quiz/creer', 'AdminController', 'storeQuiz', ['admin', 'csrf']);
$router->add('admin/quiz/modifier', 'AdminController', 'updateQuiz', ['admin', 'csrf']);

// Plans
$router->add('admin/plans', 'AdminController', 'plans', ['admin']);
$router->add('admin/plans/creer', 'AdminController', 'storePlan', ['admin', 'csrf']);
$router->add('admin/plans/modifier', 'AdminController', 'updatePlan', ['admin', 'csrf']);

// Subscriptions
$router->add('admin/abonnements', 'AdminController', 'subscriptions', ['admin']);

// Blog
$router->add('admin/blog', 'AdminController', 'blogPosts', ['admin']);
$router->add('admin/blog/creer', 'AdminController', 'storeBlogPost', ['admin', 'csrf']);
$router->add('admin/blog/modifier', 'AdminController', 'updateBlogPost', ['admin', 'csrf']);

// Events
$router->add('admin/evenements', 'AdminController', 'events', ['admin']);
$router->add('admin/evenements/creer', 'AdminController', 'storeEvent', ['admin', 'csrf']);

// Contacts
$router->add('admin/contacts', 'AdminController', 'contacts', ['admin']);
$router->add('admin/contacts/modifier', 'AdminController', 'updateContact', ['admin', 'csrf']);

// Settings
$router->add('admin/parametres', 'AdminController', 'settings', ['admin']);
$router->add('admin/parametres/enregistrer', 'AdminController', 'updateSettings', ['admin', 'csrf']);

// Logs
$router->add('admin/logs', 'AdminController', 'logs', ['admin']);

// Dispatch
$router->dispatch($_SERVER['REQUEST_URI']);
