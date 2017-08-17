/**
 * Declare the paths of aspects of the CMS
 */
require.config({
    paths: {
        'bluefoot/async': 'resource/requirejs/async',

        /* Extra Resources Needed */
        'jquery': 'resource/jquery-1.11.3.min',
        'bluefoot/jquery/ui': 'resource/jquery-ui/jquery-ui.min',
        'bluefoot/mustache': 'resource/mustache.min',
        'bluefoot/html2canvas': 'resource/html2canvas',
        'bluefoot/highlight': 'resource/highlight/highlight.pack',

        /* Core Component Aliases */
        'bluefoot/ajax': 'component/core/ajax',
        'bluefoot/dragdrop': 'component/core/dragdrop',
        'bluefoot/config': 'component/core/config',
        'bluefoot/renderer': 'component/core/renderer',
        'bluefoot/hook': 'component/core/hook',
        'bluefoot/modal': 'component/core/modal',
        'bluefoot/plugins': 'component/core/plugins',
        'bluefoot/stage': 'component/core/stage',
        'bluefoot/stage/build': 'component/core/stage/build',
        'bluefoot/stage/panel': 'component/core/stage/panel',
        'bluefoot/stage/save': 'component/core/stage/save',
        'bluefoot/structural': 'component/core/structural',
        'bluefoot/template': 'component/core/template',

        /* Edit Panel */
        'bluefoot/edit': 'component/core/edit',
        'bluefoot/field/text': 'component/core/edit/fields/text',
        'bluefoot/field/select': 'component/core/edit/fields/select',
        'bluefoot/field/textarea': 'component/core/edit/fields/textarea',
        'bluefoot/field/date': 'component/core/edit/fields/date',
        'bluefoot/field/abstract': 'component/core/edit/fields/abstract',

        /* Content Types */
        'bluefoot/content-type/abstract': 'content-type/core/abstract'
    },
    map: {
        '*': {
            /* Map the abstract widget to the input type widget */
            'bluefoot/widget/abstract': 'bluefoot/field/abstract'
        }
    }
});

// The app requires the core hook system to be running very early on
require(['bluefoot/hook'], function () {

    // Declare our plugins system
    require(['bluefoot/plugins'], function (Plugins) {

        // Prepare the plugin aspect of the system
        Plugins.prepare(function () {

            // Initialize the basic config to load in plugins
            require(['bluefoot/stage', 'bluefoot/stage/build', 'bluefoot/jquery', 'bluefoot/cms-config', 'bluefoot/modal'], function (StageClass, StageBuild, jQuery, InitConfig) {

                Plugins.load('onPageLoad', function () {

                    // Detect and load any saved page builder data
                    StageBuild.init();

                    // Attach a click instance onto any button with the correct class
                    jQuery(document).on('click', InitConfig.init_button_class, function (event) {

                        /**
                         * Create a new instance of the stage
                         *
                         * Each BlueFoot instance is ran by a stage, this handles all operations of the "page builder" which
                         * is refereed to in code as the stage
                         */
                        var Stage = new StageClass();
                        Stage.init(jQuery(event.currentTarget));

                    }.bind(this));

                });

            });

        });

    });

});
