# Data Tables
Simple app allowing Administrators to upload CSV tables, that the Users can view (and sort, filter) after authentication. 
Administrators can control what tables the User can access.

## How to start
Install dependencies:
```
composer install
```

If you use docker, you can create the containers using docker-compose tool:
```
docker login registry.empressia.pl
docker-compose up -d
```

Update database schema and create the first Administrator:
```
docker-compose exec web bin/console doctrine:schema:update --force
docker-compose exec web bin/console create-user --admin email@example.com
```

Now the app should be accessible on http://localhost.

## Mail Notifications
User receives an e-mail when:
- their account has been created
- they have been granted access to a table

If you use `docker-compose.yaml` file included in this repository, you can see your emails
on http://localhost:8025 without actually sending them. This is thanks to [MailHog](https://github.com/mailhog/MailHog) service.

## How to customize
### Edit layouts
The main layout is defined in Twig template [templates/base.html.twig](templates/base.html.twig) 
and there is admin base layout extending the global base [templates/admin/base.html.twig](templates/admin/base.html.twig).

If you are not familiar with Twig templating engine, [Symfony documentation](https://symfony.com/doc/current/templates.html) is a good start! 

### Create CSS
If you do not need fancy tools like webpack, etc. you can adjust styles by creating CSS files in `public` directory. Then you can link them in in global template
like this:
```
 {% block stylesheets %}
     ...
     <link rel="stylesheet" href="/css/style.css">
 {% endblock %}
```

or inside view templates extending the base template (if you want to use some styles only on given subpage) like this:
```
{% block stylesheets %}
     {{ parent() }}
     <link rel="stylesheet" href="/css/subpage.css">
 {% endblock %}
```

### Modify e-mails
#### Contents
Check out [templates/email](templates/email) directory. These are regular Twig templates.

#### Subject
Subjects are translatable, so you can modify them like other texts in [yaml file](translations/messages+intl-icu.en.yaml).

#### More
If you want to have more control on what emails look like, check out [App\MailNotification](src/MailNotification) 
namespace where e-mail notifications logic is defined.

### Translations
All texts displayed to the User are supposed to be translatable. 
You can edit translations by modifying [YAML files](translations). You can read more on how to create robust translations
on [Symfony Documentation](https://symfony.com/doc/current/translation/message_format.html).
