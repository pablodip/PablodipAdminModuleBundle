# Basic Usage

The basic sequence of actions for using the admin bundle is simply:

1. Create a bundle for your admin code
2. Create a module
3. Configure your routing file to retrieve routing information from the module

(At some point you'll need to create an entity class too - that's not AdminModule specific.)

## Create a bundle

Just create a bundle as normal (Symfony has a [generator](http://symfony.com/doc/current/bundles/SensioGeneratorBundle/commands/generate_bundle.html) if that helps).  The link with the admin vendor bundle is made by extending the relevant class when you create a module; you don't have to do anything special at this stage.

## Create a module

The module can be thought of as a link between your entity class (be that a Doctrine one or whatever else) and the admin bundle, allowing the bundle to automatically generate the pages which relate to that entity.  Put it in your new bundle.

The code below shows a simple sample module, with comments explaining what each bit does:

    <?php

    namespace MyProj\AdminBundle\Module;

    use Pablodip\AdminModuleBundle\Module\AdminModule;
    use Pablodip\ModuleBundle\Extension\Molino\DoctrineORMMolinoExtension; // this example uses Doctrine

    class MyProjUserAdminModule extends AdminModule
    {
        /**
         * Adds the Molino extension required for this
         * module
         *
         * @return DoctrineORMMolinoExtension
         */
        protected function registerMolinoExtension()
        {
            return new DoctrineORMMolinoExtension(); // persistence settings - Molino abstracts the persistence.
        }

        /**
         * Configure our admin module
         * The configure function is the key one in setting up a module.
         */
        protected function configure()
        {
            $this
                ->setRouteNamePrefix('admin_users_') // routes are based on actions - so will have admin_users_list, for example
                ->setRoutePatternPrefix('/admin/users') // what URL will look like /admin/users/[actionname], but bear in mind that "list" will simply be at /admin/users
            ;

            // There are lots of sample actions built in; see vendor/pablodip/AdminModuleBundle/Module/[AdminModule.php](https://github.com/whiteoctober/PablodipAdminModuleBundle/blob/master/Module/AdminModule.php)
            // and look for $this->addActions

            // This is the link with the entity.  You'll need to create the entity separately.  Exactly how you do this will depend on what ORM/ODM you are using.
            // PHP namespace rather than Doctrine-style
            $this->setOption('model_class', 'MyProj\CoreBundle\Entity\User');

            // If you were creating a custom action or overriding one of the standard ones, you could add it here
            // e.g. $this->addAction(new \MyProj\AdminBundle\Action\UpdateAction()); to override the update action
            // or $this->addAction(new \MyProj\AdminBundle\Action\User\NotifyAction()); to create an entirely new one

            // You have to explicitly say which model fields you want from the entity
            // $modelFields is obtained by reference so don't have to pass back after making changes
            $modelFields = $this->getOption('model_fields');
            $modelFields->add(array(
                'emailAddress' => array('label' => 'Email address'),
                'createdAt' => array('label' => 'Created at', 'date_format' => 'd M Y, g.ia', 'template' => 'PablodipAdminModuleBundle::fields/date.html.twig'), // you can use custom templates for rendering fields
            ));

            // configuration of the actions has been split into separate methods to avoid having a huge configure method!
            $this->configureList();
            $this->configureNewCreate();
            $this->configureEditUpdate();
        }

        // The methods below configure specific actions.

        /**
         * Configuration for the list action
         */
        protected function configureList()
        {
            $action = $this->getAction('list');
            $action->getOption('list_fields')->add(array('emailAddress')); // any fields added here must exist in modelFields array
            $action->getOption('list_fields')->add(array('createdAt'));
            $action->setOption('heading', 'User list');
        }

        /**
         * Configuration for the new/create actions
         */
        protected function configureNewCreate()
        {
            $newAction = $this->getAction('new');
            $modelFields = $this->getOption('model_fields');
            $newAction->getOption('fields')->add($modelFields->keys());
            $newAction->setOption('heading', 'New user');
        }

        /**
         * Configuration for the edit/update actions
         */
        protected function configureEditUpdate()
        {
            $editAction = $this->getAction('edit');
            $updateAction = $this->getAction('update');
            $editAction->setOption('heading', 'Edit user');
            $editAction->getOption('fields')->add(array("emailAddress"));
            $updateAction->setOption('success_text', 'Changes saved');
            $updateAction->setOption('error_text', 'There were problems saving your changes');

            // Event configuration
            $updateAction->setOption("event_name", "myproj.entity.user.updated");
            $updateAction->setOption("event_class", "MyProj\\CoreBundle\\Event\\UserUpdatedEvent");
            $updateAction->setOption("event_method", "setUser");
        }
    }

## Configure routing file

Just add an entry like this:

    myproj_admin_modules:
      resource: "@MyProjAdminBundle/Module"
      type:     module

The name can be anything you like, and the resource entry should point to the bundle you created (not to the base one in vendors).

You now browse to the appropriate routes for your module's actions!  (See `setRoutePatternPrefix` above.)

# What else might I see?

If you're looking at the implementation of the AdminBundle in an existing project, you might see lots of directories in the bundle.  The only Admin-specific stuff is Action, Filter and Module, everything else is standard Symfony2 stuff.

* Action - only need if have custom actions (see the comment in the code about "sample actions" for details of the default ones).
* Filter - by default, lists in the admin bundle include basic filtering.  You can create more advanced filters yourself.
* Module - your modules as described above.

_Next: [Advanced Usage](advanced-usage.md)_

_Back to [README.md](../README.md)_
