const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
mix.disableSuccessNotifications();

mix.js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css')
   .sass('resources/sass/landing-page.scss', 'public/css')
   .sass('resources/sass/app-global.scss', 'public/css')
   .sass('resources/sass/register.scss', 'public/css')
   .sass('resources/sass/login.scss', 'public/css')
   .sass('resources/sass/profile.scss', 'public/css')
   .sass('resources/sass/materialize.scss', 'public/css')
   .sass('resources/sass/questions.scss', 'public/css')
   .sass('resources/sass/quizzes.scss', 'public/css')
   .sass('resources/sass/dashboard.scss', 'public/css')
   .sass('resources/sass/classes.scss', 'public/css')
   .sass('resources/sass/users.scss', 'public/css')
   .sass('resources/sass/accompanying_questions.scss', 'public/css')
   .sass('resources/sass/additional_messages.scss', 'public/css')
   .sass('resources/sass/running_quiz.scss', 'public/css')
   .sass('resources/sass/monitor_quiz.scss', 'public/css')
   .sass('resources/sass/export_quiz.scss', 'public/css')
   .sass('resources/sass/quiz_results.scss', 'public/css')
   .sass('resources/sass/leaderboard.scss', 'public/css')
   .sass('resources/sass/quiz_scheduling.scss', 'public/css')
   .sass('resources/sass/quiz_start.scss', 'public/css')
   .sass('resources/sass/quiz_presentation.scss', 'public/css')
   .sass('resources/sass/admin_users.scss', 'public/css')
   .sass('resources/sass/admin_classes.scss', 'public/css')
   .sass('resources/sass/admin_quizzes.scss', 'public/css')
   .sass('resources/sass/admin_manage_user.scss', 'public/css')
   .sass('resources/sass/admin_user_view.scss', 'public/css')
   .sass('resources/sass/admin_edit_class.scss', 'public/css')
   .sass('resources/sass/admin_edit_quiz.scss', 'public/css')
   .sass('resources/sass/scores.scss', 'public/css')
   .sass('resources/sass/quiz_game.scss', 'public/css')


    .js('resources/js/register.js', 'public/js')
   .js('resources/js/materialize.min.js', 'public/js')
   .js('resources/js/questions.js', 'public/js')
   .js('resources/js/quizzes.js', 'public/js')
   .js('resources/js/quizzes_participants.js', 'public/js')
   .js('resources/js/create_question.js', 'public/js')
   .js('resources/js/create_quiz.js', 'public/js')
   .js('resources/js/profile.js', 'public/js')
   .js('resources/js/edit_profile.js', 'public/js')
   .js('resources/js/classes.js', 'public/js')
   .js('resources/js/classes_participants.js', 'public/js')
   .js('resources/js/class_after_creation.js', 'public/js')
   .js('resources/js/users.js', 'public/js')
   .js('resources/js/add_quizzes_modal.js', 'public/js')
   .js('resources/js/monitor_quiz.js', 'public/js')
   .js('resources/js/accompanying_questions.js', 'public/js')
   .js('resources/js/additional_messages.js', 'public/js')
   .js('resources/js/dashboard.js', 'public/js')
   .js('resources/js/running_quiz.js', 'public/js')
   .js('resources/js/export_quiz.js', 'public/js')
   .js('resources/js/quiz_results.js', 'public/js')
   .js('resources/js/quiz_scheduling.js', 'public/js')
   .js('resources/js/quiz_start.js', 'public/js')
   .js('resources/js/leaderboard.js', 'public/js')
   .js('resources/js/scores.js', 'public/js')
   .js('resources/js/admin_users.js', 'public/js')
   .js('resources/js/admin_classes.js', 'public/js')
   .js('resources/js/admin_quizzes.js', 'public/js')
   .js('resources/js/admin_manage_user.js', 'public/js')
   .js('resources/js/admin_user_view.js', 'public/js')
   .js('resources/js/quiz_game.js', 'public/js')
   .js('resources/js/quiz_presentation.js', 'public/js')
   .js('resources/js/admin_validate_teachers.js', 'public/js')
    .version();

