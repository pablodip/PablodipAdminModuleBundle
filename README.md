# PablodipAdminModuleBundle

This is the White October fork of https://github.com/pablodip/PablodipAdminModuleBundle

The master branch tracks the Symfony2 master branch.

## Author

Pablo Díez - <pablodip@gmail.com>

## License

PablodipAdminModuleBundle is licensed under the MIT License. See the LICENSE file for full details.

## Basic Usage

The basic sequence of actions for using the admin bundle is simply:

1. Create a bundle for your admin code
2. Create a module
3. Configure your routing file to retrieve routing information from the module

(At some point you'll need to create an entity class too - that's not AdminModule specific.)

### Create a bundle

Just create a bundle as normal (Symfony has a [generator](http://symfony.com/doc/current/bundles/SensioGeneratorBundle/commands/generate_bundle.html) if that helps).  The link with the admin vendor bundle is made by extending the relevant class when you create a module; you don't have to do anything special at this stage.

### Create a module

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

### Configure routing file

Just add an entry like this:

    myproj_admin_modules:
      resource: "@MyProjAdminBundle/Module"
      type:     module

The name can be anything you like, and the resource entry should point to the bundle you created (not to the base one in vendors).

You now browse to the appropriate routes for your module's actions!  (See `setRoutePatternPrefix` above.)

## What else might I see?

If you're looking at the implementation of the AdminBundle in an existing project, you might see lots of directories in the bundle.  The only Admin-specific stuff is Action, Filter and Module, everything else is standard Symfony2 stuff.

* Action - only need if have custom actions (see the comment in the code about "sample actions" for details of the default ones).
* Filter - by default, lists in the admin bundle include basic filtering.  You can create more advanced filters yourself.
* Module - your modules as described above.


## More advanced usage

### Custom form fields

By default, the module will render you textboxes, checkboxes and drop-downs for dates, all worked out from the value you set for `template`.  However, you can instruct it to use any of [Symfony2's form types](http://symfony.com/doc/current/reference/forms/types.html) thanks to the `form_type` property!  You can also pass through settings for these types.  Here's an example:

    $modelFields->add(array(
        'votesPercent' => array(
            'label' => 'votes_percent',
            'form_type' => 'percent',
            'template' => 'PablodipAdminModuleBundle::fields/text.html.twig',
            'form_options' => array('required' => false, 'type' => 'integer')
        ),
    ));

### Parent/child relationships

Say that you have an admin module for a Region, and each of those Region has one or more Countries.  In such a "parent/child" situation, when the admin bundle creates you new Countries, it needs to set the details of their parent Region.  Fortunately, you can use the _MolinoNestedExtension_ to handle a lot of this for you!  This section explains how.

#### Step 1: Create your child module PHP file as normal

The only difference is that you'll have some sort of references entry for the child object, relating it back to its parent.

In Doctrine, for example, you might have this:

    /**
     * @ORM\ManyToOne(targetEntity="Region")
     * @var integer
     */
    protected $region;

A Mandango schema might look like this:

    Model\MyProjCoreBundle\Country:
       fields:
           language: string
           timezone: string
       referencesOne:
           region: { class: Model\MyProjCoreBundle\Region, reference: region }

This sort of thing isn't AdminBundle-specific, but the later steps are.

#### Step 2: Set up MolinoNestedExtension

Add some code like the following into your child module's class:

    protected function registerExtensions()
    {
        $extensions = parent::registerExtensions();
        $extensions[] = new MolinoNestedExtension(array(
            'parent_class'      => "Model\\MyProjCoreBundle\\Region",
            'route_parameter'   => "programme_id",
            'query_field'       => "programme",
            'association'       => "programme",
        ));

        return $extensions;
    }

You can read more about extensions in general at https://github.com/whiteoctober/PablodipModuleBundle#extensions

If you look in the MolinoNestedExtension code, you can see that it does various things:

1. Before the child controller executes, the extension looks up the parent in the database and makes the parent available as an attribute on the request (see `addCheckParentControllerPreExecute`).  NB You can't retrieve this value in the Module's `configure` function - it won't be available at that point.
2. When queries are run for the child object, [Molino events](https://github.com/whiteoctober/molino#events) are used to add a criteria ensuring that the parent is matched too (`addCreateQueryEvent`).
3. Similarly, when the child object is saved, the parent reference is automatically set (`addCreateModelEvent`).

You'll also need to add some Molino config into your module.  Something like this:

    protected function registerMolinoExtension()
    {
        $eventDispatcher = $this->registerMolinoEventDispatcher();

        return new DoctrineORMMolinoExtension($eventDispatcher);
    }

    protected function registerMolinoEventDispatcher()
    {
        return new EventDispatcher();
    }

#### Step 3: Set up route

You'll notice that one of the objects passed to the MolinoNestedExtension constructor is a route parameter.  This allows the extension to work out which parent object the child object belongs to by looking at the route.

Therefore, you'll need to ensure that your child object's route prefix is set up appropriately.  Continuing the example above, you might use:

    $this->setRoutePatternPrefix('/admin/regions/{region_id}/countries');

#### Step 4: Modify templates

To actually make use of this functionality, you'll need to give some way of accessing the appropriate new/edit pages for your child objects, and some way of listing them.  Exactly how you do this is up to you; this section shows one way to do it

For creating new objects, you could add the following link to the parent's template, inside the loop which generates code for each parent object:

    <a href="{{ path('admin_countries_new', { 'region_id': model.id } ) }}">{% trans %}add_country{% endtrans %}</a>

The `trans` tags are only required if your application does localisation (which it probably should!)

For listing and edited them, you could created a separate controller method which rendered a template within the parent template:

    {% render "MyProjAdminBundle:Default:countryList" with {'region_id': model.id} %}

That template then created edit links in a similar way to the new link shown above.

#### Multiple levels of nesting

Multiple levels of nesting are supported.  In this sort of context, you might have a route like this:

    $this->setRoutePatternPrefix('/regions/{region_id}/countries/{country_id}/district')

One of the parameters here will be used when registering the Molino nested extension.  In order for the other parameter to work, you'll need to call `addParameterToPropagate` when setting up the routing in your `configure` method.  E.g:

    $this->addParameterToPropagate('region_id');

This only needs doing for the parameter which isn't mentioned in the MolinoNestedExtension constructor.

`addParameterToPropagate` can also be used for passing other parameters into your templates.  You can then retrieve them with `app.request.attributes.get('blah')`

If you need to pass in something more complicated, use a [module option](https://github.com/whiteoctober/PablodipModuleBundle#options).

### Passing additional parameters into templates

You have three options:

1. For the values of things used in the route (e.g. IDs), use `addParameterToPropagate` and retrieve with `app.request.attributes.get('blah')`.

2. For passing in more complex objects, use a [module option](https://github.com/whiteoctober/PablodipModuleBundle#options).  You are limited by the data available to the module in terms of what you can pass in.

3. For ultimate flexibility, define a custom route action.

### Filters (advanced search)

Want to offer a standard filter interface to your admin users? Easy peasy.  First make sure any fields that you want to filter on have been added in your admin module in the normal way via the `model_fields` option. Now, there are two approaches...

If you want to filter via a normal string `LIKE '%foo%'`-style approach, or a boolean field, add the following into your field definition:

    'advanced_search_type' => 'string',

or

    'advanced_search_type' => 'boolean',

or

    'advanced_search_type' => 'integer',

depending on your requirements.  This will give you a standard set of widgets accessible via the Advanced Search container on the default list page.

Alternatively, if you want to add some kind of custom filter (eg a date filter, a dropdown populated by your own special items and so on), you'll need to create a custom filter class, which extends from `Pablodip\AdminModuleBundle\Filter\BaseFilter`.  This has 3 methods that will need to be implemented - see the base filters for an example.  Essentially you're just creating a form, and then applying any values submitted to a supplied Molino Query object to perform the filtering.

Once you've configured your fields, the final step is to tell your `ListAction` that you want to add fields to the search filter.  This is done via the action's `advanced_search_fields` option (shown here being configured within a module):

    $listAction = $this->getAction('list');
    $modelFields = $this->getOption('model_fields');
    $listAction->getOption('advanced_search_fields')->add(array(
        ...,
        'yourFieldName' => $modelFields->get('yourFieldName'),
        'anotherField' => $modelFields->get('anotherField'),
        ...,
    ));

You'll be able to filter now from the list page in your module.

#### GOTCHA: "is not" and null columns

In the course of writing your filters, you'll probably include some code like this:

        if ($data['type'] == 'is') {
            $query->filterEqual('column-name-here', $data['value']);
        }
        if ($data['type'] == 'is not') {
            $query->filterNotEqual('column-name-here', $data['value']);
        }

In many cases, this will do what you want.  However, bear in mind cases like the following example:

1. You have a nullable column called "payment method"
2. 8 entities have this as null, 1 has this set to "Credit Card" and 1 has this set to "Debit Card"
3. You filter "Payment method is not Credit Card"

How many results should be returned?  The simple code above will return 1 result, that with a payment method of "Debit Card".  If you want nulls to match too, you'll need to write some more complicated code.  It'll look something like this:

    if ($data['type'] == 'is') {
        $query->filterEqual('paymentMethod', $data['value']);
    }
    if ($data['type'] == 'is not') {
        // Have to build a more complex queries, so that null counts as "not equal to anything"
        // Doctrine's SelectQuery has the getQueryBuilder method (from BaseQuery), even though the QueryInterface doesn't
        /* @var $qb QueryBuilder */
        $qb = $query->getQueryBuilder();

        // any existing conditions?
        $existingWhere = $qb->getDQLPart('where');
        if ($existingWhere) {
            $existingWhere = $existingWhere->getParts();
        }

        // get table aliases so can associate the column with a table
        $aliases = $qb->getRootAliases();
        $colNameAndIdentifier = $aliases[0] . '.paymentMethod';

        // WHERE [any existing conditions] AND (paymentMethod <> ?x OR paymentMethod IS NULL)
        $or = new Orx(
            array(
                $qb->expr()->neq($colNameAndIdentifier, ':paymentMethod'),
                $qb->expr()->isNull($colNameAndIdentifier)
            )
        );

        if ($existingWhere) {
            $conditions = new Andx(
                $existingWhere
            );
            $conditions->add($or);
        } else {
            $conditions = $or;
        }
        $qb->add('where', $conditions);

        $qb->setParameter('paymentMethod', $data['value']);
    }

## Troubleshooting

### Row actions (e.g. edit, delete) are missing

If you add a custom list action, you'll find that items in your admin list no longer have the links to edit and delete.  This is because those actions will have been added to the `list_action` list for the original list action, not for your custom one.

Your code which adds the custom list action will therefore need to explicitly re-add them:

    protected function defineConfiguration()
    {
        parent::defineConfiguration();

        $this->addActions(array(
            new CustomListAction(), // this one is because of extra default filtering on orders

            // Have to explicitly re-add edit and delete actions so that their links are added to
            // our new list action (since we added a custom list action above)
            new EditAction(),
            new DeleteAction(),
        ));
    }

## See also

The Admin bundle builds on top of Molino and Pablo's generic ModuleBundle.  These are documented:

* Molino docs: https://github.com/whiteoctober/molino
* ModuleBundle docs: https://github.com/whiteoctober/PablodipModuleBundle
