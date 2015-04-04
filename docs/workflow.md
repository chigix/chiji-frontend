% Suggested Workflow for Front-end Project
% Richard Lea (chigix@zoho.com)

# The Directory Structure

Initially then, the directory structure of a project is not only one case. Here is just a case as suggestion for more general project with front-end developing requirement. If you are using other MVC Framework like Symfony, of course you should try to intergrate the directory into the framework.

    +app/
        +scripts
            +controllers
                -...
            -app.js
        +styles
            +main.css
        +images
            -logo.png
        +views
            -about.html
            -main.html
        -index.html
        -404.html
        -favicon.ico
        -robots.txt
    +bower_components/
    +node_modules/
    +test/
    -.bowerrc
    -.gitignore
    -.jshintrc
    -bower.json
    -Gruntfile.js
    -RoboFile.php
    -npm-debug.log

* The `app` directory is the main space where front-end assetic files put.
* The `bower_components` directory is the default name for bower components management.
* The `node_modules` directory is the default name for node packages management.
* The `test` directory is for the project with a scaffolded out test runner and the unit tests, including boilerplate tests for our controllers.
* The `.bowerrc` is the bower config file including the bower components directory name configuration.
* The `.gitignore` should exclude the `node_modules` and `bower_components` directory which should be used only in local development.
* The `.jshintrc` is the config file for JShint script.
* The `bower.json`  is the manifest file for tracking bower packages.
* The `Gruntfile.js` , `gulpfile.js` and `RoboFile.php` are all the auto task runner scripts, the hero character for the modern front-end project building.

The Chiji-Frontend Intergration solution is written in PHP though, but the solution aimed is to cover all web project cases, such as JAVA Web, JAVA EE, Python Web App and so on. Besides the usage of bower, grunt/gulp is important, developers should raise the utils from open-source communication and their giant plugins ecology.

This Project, the Chiji-frontend package is just a component written in PHP, but it's not the factor matters developers.

# Work With Bower

[Bower](http://bower.io) provides the management for all the things making up web sites such as frameworks, libraries, assets, utilities, and rainbows.

Before using bower, please make sure the node and npm installed in your local system.

Developers should just put a `bower.json` file in the project for saving and using packages happily. Optionally, the `.bowerrc` file could be set to specify another directory name for packages installing such as `vendor`.

All the files from bower could be registered as resources into chiji-frontend and be required into the project.

# Resource Processing

In Chiji Frontend Workflow, there is two type of processing for resources: Resource Building and Resource Releasing.

Although building and releasing are the same level processing at target resource file, the cache from building has more internal relation embeded. So we suggest developer to check newly created files looply during cache building, on the contrary, to put final touch directly to desitination.

For more information for the two processing, please move to the [RoadMap](./roadmap.md) documents checking corresponding sections.