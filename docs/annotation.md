% Annotation
% Richard Lea (chigix@zoho.com)

# Annotation

In chiji frontend solution project, the **ANNOTATION** is the greatest part from all.

Basically, the original chiji framework provide some utility annotation for resource required declaration, resource referenecing code to write and resource releasing upon roadmap.

* `@require` - Statement for the resource requirement.
* `@use` - Statement for namespace in single file scope.
* `@release` - Release a specified resource through matched road.

ReleaseManager-->PLUGIN, HTTP-ReleaseDir Mapping
ReleaseUtil-->dist();distconcat();

TWIG SUPPORT:  {{ chiji.release.dist("../a.less") }} --> ANNOTATION + URL
ANNOTATION SUPPORT: 

$this->taskSource($project);
$this->taskBuild($project);
$this->taskRelease($project);