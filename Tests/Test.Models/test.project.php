<?php

require __DIR__ . '/../../vendor/autoload.php';

use App\Model\Project;

function testCreateProject() {
	$project = new Project(1); // creating a project for kylo_ren
	if(
		$project->createProject(
			array(
				"created_by" => 1, 
				"project_name" => "Add new functions to user controller and project controller",
				"description" => "Create the Project controller class and add the additional functionlities to the user controller class",
				"field" => "IT and Technological"
			)
		)
) {
		echo "<pre>";	
		echo "Project created successfully";	
		echo "</pre>";	
	} else {
		echo "<pre>";	
		echo "Project cannot be created";	
		echo "</pre>";
	}
}

function testReadAllTheProjectsOfUser() {
	$project = new Project(1); // reading the projects for kylo_ren
	if($project->readProjectsOfUser(1)) {
		echo "<pre>";
		var_dump($project->getProjectData());
		echo "</pre>";	
	} else {
		echo "<pre>";	
		echo "Project reading failed";	
		echo "</pre>";
	}

}

function testReadSingleProjectOfUser() {
	$project = new Project(1); // reading the projects for kylo_ren
	if($project->readProjectsOfUser(1, 1)) {
		echo "<pre>";
		var_dump($project->getProjectData());
		echo "</pre>";	
	} else {
		echo "<pre>";	
		echo "Project reading failed";	
		echo "</pre>";
	}

}

testCreateProject();
testReadAllTheProjectsOfUser();
testReadSingleProjectOfUser();