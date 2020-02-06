# DLM

DLM is a tool for creating e-learning modules. It is a LTI-compliant provider, based on Laravel (5.4). Requirements:
- Webserver
- PHP 7.0 or higher
- MySQL

For installation / development:
- Git
- Composer (https://getcomposer.org)
- NPM (https://www.npmjs.com/get-npm)

## Installation
Since the application is based on laravel, please also consult the installation documentation at https://laravel.com/docs/5.4/installation.

- Clone the repository 
- `composer install --no-scripts --no-dev` (remove composer.lock if this doesn't work)
- create a .env-file with database details etc. `cp .env-example .env` See https://laravel.com/docs/5.4/installation#configuration
- `php artisan key:generate` This will generate a secret key, which is stored in the .env-file.
- `npm install` 
- `npm run production`
- `php artisan migrate`
- `php artisan storage:link` This will create a symlink from `public/storage` to the `storage/app/public` directory. The entire `storage` directory should be writeable for the application user.
- `php artisan create:adminuser {username} {email} {password}` to create your first admin user

## Setup
Besides the admin user account (which has read/write access to all content) all user accounts are created by linking to an LMS using LTI. DLM acts as an LTI-provider. The admin user can setup allowed consumers via the `https://<application-domain>/lti/consumers` link. Adding a consumer requires a consumer domain and a name. The name can be chosen freely, the domain needs to be the domain of the LMS. After creating a consumer, a consumer secret will be generated.

On the consumer side, settings for Moodle:
- the tool-URL for DLM is `https://<application-domain>/lti`
- The consumer key is the domain of the Moodle application
- the shared secret needs to be the secret generated in DLM.

Linking to a module in DLM needs to follow this format: `https://<application-domain>/lti/<module-id>`. The module ID can be found at the end of the URL when opening a module in DLM. The regular URL for a module follows this format: `https://<application-domain>/module/<module-id>`

## Roles
User roles are mapped as follows:
Moodle role (archetype), LTI role, DLM rights: 
- Teacher, urn:lti:role:ims/lis/Instructor, view and edit (module level)
- Administrator, urn:lti:instrole:ims/lis/Administrator, urn:lti:sysrole:ims/lis/Administrator, view and edit (application level)
- Student, urn:lti:role:ims/lis/Learner, view (module level)
