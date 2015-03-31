Chiji Front-end The Base Package
==============

* [Composer](https://packagist.org/packages/chigix/chiji)

Chiji is a PHP Component for providing the best Front-end Intergration Solution.

# Getting Started

Chiji Base Package is a PHP Library though, it is designed to all front-end project case within JAVA Web, PHP MVC Framework, Single Page Application etc.

Originally, this Base Package is just a toolset with Project, annotation, SourceRoad classes, helping users reorganize front-end resources in a WEB project. Of course, there is a custom PHP script as a main entry in need to launch tasks for assets distributing.

Conclusionly, the PHP knowledge is required for all users (no matter UED engineers or this package contributors), if however, we also provide some bridge packages to common frameworks or cases, such as [Chiji-Bundle](https://packagist.org/packages/chigix/chiji-bundle) for Symfony users.

To be relax, the installation is the easiest. Every thing (including bridge packages) could be installed via Composer, which is just a linux command tool without any PHP details. The only technical requisite to step into the next secion, installation, is to have PHP 5.4 or higher installed on your composer. If you use a packaged PHP solution such as WAMP, XAMP or MAMP, chekcout that they are PHP5.4 or a more recent version. You can also execute the following command in your terminal or command console to display the installed PHP version:

    $ php --version

# Installing Chiji-Frontend

Globally, everything in the official Chiji-Frontend Circles should be submitted in [Packagist](https://packagist.org), so you can use [Composer](https://getcomposer.org/) directly to finish the installation task:

    $ composer require chigix/chiji:~1.0.0

If you are using some fashion frameworks or want other bridge packages, you could check details from the corresponding project.

# Main Point

In the Chiji Solution, the front-end project could be resolved, around the resources, as the three partition:

1. Resource Positioning
2. Resource Relation
3. Resource Releasing

Every assetic file (css, js, scripts, images) are treated as Resources located into one Project. Then the project would try to find target assetic files and registered as mapped resource object through SourceRoad, which with responsible for Assetic Pre-building and Releasing Flow.

Annotation is the main util for the requirement relationships between resources. More freely, Customer annotation could be implemented to cover more resource operation requisites and cases.

For users, the design on resources operation flow and releasing schedule is the main task, maybe you can have a look at the Symfony Chiji-bundle bridge repository.

# Basic API Usage

This section gives you a brief introduction to the PHP API for Chiji the Base Package.

```php
<?php
// /path/to/conf-file.php

class ConfigFile extends \Chigi\Chiji\Project\ProjectConfig {

}

return new ConfigFile();
?>
```

Initially then, the configuration file for the front-end project is needed to create with defining the source roads. Defaultly, the original `\Chigi\Chiji\Project\ProjectConfig` abstract class could be used as the start kit directly.

```php
require_once '/path/to/vendor/autoload.php'

$project = new \Chigi\Chiji\Project\Project("/path/to/conf-file.php");
\Chigi\Chiji\Util\ProjectUtil::registerProject($project);
foreach ($project->getReleaseDirs() as $dir) {
    // ... Clean and init release directories.
}
foreach ($project->getSourceDirs() as $dir) {
    // ... scan the directory for register files as resource.
    $project->getResourceByFile($file);
    // ...
}
foreach ($project->getRegisteredResources() as $resource) {
    if ($resource instanceof \Chigi\Chiji\File\Annotation) {
        $resource->analyzeAnnotations();
    }
}
foreach ($project->getReleasesCollection() as $resource) {
    $project->getMatchRoad($resource->getFile())->releaseResource($resource);
}
```

After registering the configured project, the operations for resources could be scheduled:

1. Resources Positioning through defined Source Roads in project.
2. Resources Relating through annotations embeded in resource codes.
3. Resources Releasing through defined Source Roads in project.

# More Information

Read the [docs](./docs) for more information.