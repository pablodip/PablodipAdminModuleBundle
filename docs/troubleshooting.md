# Troubleshooting

## Row actions (e.g. edit, delete) are missing

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

_Previous: [Advanced Usage](advanced-usage.md)_

_Back to [README.md](../README.md)_
