<?php
/* (c) Anton Medvedev <anton@medv.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deployer;

require_once __DIR__ . '/common.php';

set('log_files', 'storage/logs/*.log');

// Laravel shared dirs
set('shared_dirs', [
    'storage',
]);

// Laravel shared file
set('shared_files', [
    '.env',
]);

// Laravel writable dirs
set('writable_dirs', [
    'bootstrap/cache',
    'storage',
    'storage/app',
    'storage/app/public',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
]);

/**
 * Helper tasks
 */
desc('Disable maintenance mode');
task('artisan:up', function () {
    $output = run('if [ -f {{deploy_path}}/current/artisan ]; then {{bin/php}} {{deploy_path}}/current/artisan up; fi');
    writeln('<info>' . $output . '</info>');
});

desc('Enable maintenance mode');
task('artisan:down', function () {
    $output = run('if [ -f {{deploy_path}}/current/artisan ]; then {{bin/php}} {{deploy_path}}/current/artisan down; fi');
    writeln('<info>' . $output . '</info>');
});

desc('Execute artisan migrate');
task('artisan:migrate', function () {
    run('{{bin/php}} {{release_path}}/artisan migrate --force');
})->once();

desc('Execute artisan migrate:fresh');
task('artisan:migrate:fresh', function () {
    run('{{bin/php}} {{release_path}}/artisan migrate:fresh --force');
});

desc('Execute artisan migrate:rollback');
task('artisan:migrate:rollback', function () {
    $output = run('{{bin/php}} {{release_path}}/artisan migrate:rollback --force');
    writeln('<info>' . $output . '</info>');
});

desc('Execute artisan migrate:status');
task('artisan:migrate:status', function () {
    $output = run('{{bin/php}} {{release_path}}/artisan migrate:status');
    writeln('<info>' . $output . '</info>');
});

desc('Execute artisan db:seed');
task('artisan:db:seed', function () {
    $output = run('{{bin/php}} {{release_path}}/artisan db:seed --force');
    writeln('<info>' . $output . '</info>');
});

desc('Execute artisan cache:clear');
task('artisan:cache:clear', function () {
    run('{{bin/php}} {{release_path}}/artisan cache:clear');
});

desc('Execute artisan config:cache');
task('artisan:config:cache', function () {
    run('{{bin/php}} {{release_path}}/artisan config:cache');
});

desc('Execute artisan route:cache');
task('artisan:route:cache', function () {
    run('{{bin/php}} {{release_path}}/artisan route:cache');
});

desc('Execute artisan view:clear');
task('artisan:view:clear', function () {
    run('{{bin/php}} {{release_path}}/artisan view:clear');
});

desc('Execute artisan view:cache');
task('artisan:view:cache', function () {
    run('{{bin/php}} {{release_path}}/artisan view:cache');
});

desc('Execute artisan event:cache');
task('artisan:event:cache', function () {
    run('{{bin/php}} {{release_path}}/artisan event:cache');
});

desc('Execute artisan event:clear');
task('artisan:event:clear', function () {
    run('{{bin/php}} {{release_path}}/artisan event:clear');
});

desc('Execute artisan optimize');
task('artisan:optimize', function () {
    run('{{bin/php}} {{release_path}}/artisan optimize');
});

desc('Execute artisan optimize:clear');
task('artisan:optimize:clear', function () {
    run('{{bin/php}} {{release_path}}/artisan optimize:clear');
});

desc('Execute artisan queue:restart');
task('artisan:queue:restart', function () {
    run('{{bin/php}} {{release_path}}/artisan queue:restart');
});

desc('Execute artisan horizon:terminate');
task('artisan:horizon:terminate', function () {
    run('{{bin/php}} {{release_path}}/artisan horizon:terminate');
});

desc('Execute artisan storage:link');
task('artisan:storage:link', function () {
    run('{{bin/php}} {{release_path}}/artisan storage:link');
});

/**
 * Task deploy:public_disk support the public disk.
 * To run this task automatically, please add below line to your deploy.php file
 *
 *     before('deploy:symlink', 'deploy:public_disk');
 *
 * @see https://laravel.com/docs/5.2/filesystem#configuration
 */
desc('Make symlink for public disk');
task('deploy:public_disk', function () {
    // Remove from source.
    run('if [ -d $(echo {{release_path}}/public/storage) ]; then rm -rf {{release_path}}/public/storage; fi');

    // Create shared dir if it does not exist.
    run('mkdir -p {{deploy_path}}/shared/storage/app/public');

    // Symlink shared dir to release dir
    run('{{bin/symlink}} {{deploy_path}}/shared/storage/app/public {{release_path}}/public/storage');
});

/**
 * Run laravel specified tasks.
 */
task('deploy:laravel', [
    'artisan:storage:link',
    'artisan:view:cache',
    'artisan:config:cache',
]);

after('deploy:update_code', 'deploy:vendors');
before('deploy:symlink', 'deploy:laravel');
