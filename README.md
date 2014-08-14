#Laravel multizone ACL

This plugin allows you to separate access for users in different zones of your application. This acl plugin is database oriented. For example you have application which have administration part and front-end part, but you need both this parts to be controlled by acl separately. My plugin allows you to do this very easily.

##Installation
```
composer.phar require marks/acl dev-master
```

##Configuration
Create new file in config directory named **acl.php** and put this piece of code in this file.

```
<?php

//acl configuration file
return array(
	"max_groups" => 4, //maximum groups in one zone
	
	"zones"=>array("default"), //array of zones that you want to use
	
	"default_zone"=>"default", //default zone (have to be in array of zones)
	
	//zone specific configuration
	"default" => array(
		"model" => "User", //model
		"col" => "acl_group_id", //colum that specifies relation between model and acl group
	),
);
```


##Setup
First you need to add 
```
'Marks\Acl\AclServiceProvider'
```
to your application service providers and 
```
"Acl" => 'Marks\Acl\Facades\Acl'
```
to your application facades. This allows you to use plugin with **Acl** facade in your application.

This plugin is managed by commands so you have to issue this commands to setup the plugin

```
php artisan acl:setup //this command run database migrations and controller scanning
php artisan acl:seed //this command seeds the database and allows default group to perform any action
```

##Controllers scanning
You do not have to care about what controllers and routes you have added and think about to add them into ACL plugin. Every time you add new controller or route simply issue the command:

```
php artisan acl:scan
```

##User identification
Then you create filter in your filters.php with this example code and add filter to your routes.

```
Route::filter("acl", function(){
  //this passes the user acl group id into plugin
	Acl::default()->identify(Auth::user()->acl_group_id);
	//if user is allowed returns (bool) true if not returns (bool) false
	if(!Acl::allowed()){
	  //here goes your code for error exception
	  App::abort(403, 'Unauthorized action.');
	}
});
```

##Multiple zones
If you want to use plugin with multiple zones simply add new zone to **acl.php** configuration file and use it like that
```
Acl::zonename()->identify($id);
```

##Setting permissions
First you need to create some form of displaying the permissions of groups (aros) related to actions (acos). For that purpose you can use thoose commands:

#####List all resources (acos)
```
Acl::getResources();
```
#####List of all groups (aros) in zone
```
Acl::zonename()->getGroups();
```

To check if group is allowed to perform action you can use this method:
```
Acl::zonename()->getPermission((int) $aco_id, (int) $aro_id);
```

To set permission for specific aco and aro you can use this method:
```
Acl::zonename()->set((int) $aco_id, (int) $aro_id, (bool) $allowed);
```

##Acl user groups
This acl plugin is group oriented so each acl check is performed on group level. It means that every user has to be in acl group and plugin does not care about user id but it cares about **acl_group_id**. In examples below **zonename** will be name of your zone for example **default**.

###Add new group

```
Acl::zonename()->addGroup((string) $zonename, (bool) $isdefault);
```

###Edit existing group
```
Acl::zonename()->editGroup((int) $groupID, array("name"=> (string) $zonename, "default"=> (bool) $isdefault));
```

###Delete group
```
Acl::zonename()->deleteGroup((int) $groupID);
```

###List all groups
```
Acl::zonename()->getGroups();
```

##Zones
Those methods allows you to get list of all zones and get default zone name.

###List all zones
Method returns array of all zone names.
```
Acl::getZones();
```

###Get default zone name
Method returns default zone name as string.
```
Acl::getDefaultZone();
```




