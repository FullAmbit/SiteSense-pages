<?php
/*
* SiteSense
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@sitesense.org so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade SiteSense to newer
* versions in the future. If you wish to customize SiteSense for your
* needs please refer to http://www.sitesense.org for more information.
*
* @author     Full Ambit Media, LLC <pr@fullambit.com>
* @copyright  Copyright (c) 2011 Full Ambit Media, LLC (http://www.fullambit.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/
function pages_settings() {
	return array(
		'name'      => 'pages',
		'shortName' => 'pages',
		'version'   => '1.0'
	);
}
function pages_install($db,$drop=false,$firstInstall=false,$lang='en_us') {
	$structures=array(
		'pages' => array(
			'id'              => SQR_IDKey,
			'shortName'       => SQR_shortName,
			'name'            => SQR_name,
			'title'           => SQR_title,
			'rawContent'      => 'MEDIUMTEXT NOT NULL',
			'parsedContent'   => 'MEDIUMTEXT NOT NULL',
			'parent'	      => SQR_ID,
			'sortOrder'	      => SQR_sortOrder.' DEFAULT \'1\'',
			'live'			  => SQR_boolean,
			'KEY `shortName` (`shortName`,`parent`,`sortOrder`)'
		),
		'pages_sidebars' => array(
			'id'              => SQR_IDKey,
			'page'            => SQR_ID,
			'sidebar'         => SQR_ID,
			'enabled'         => SQR_boolean,
			'sortOrder'       => SQR_sortOrder.' DEFAULT \'1\'',
			'UNIQUE KEY `module` (`page`,`sidebar`)'
		)
	);
	if($drop)
        pages_uninstall($db,$lang);

	$db->createTable('pages',$structures['pages'],$lang);
	$db->createTable('pages_sidebars',$structures['pages_sidebars']);

	if($firstInstall){
	    // Set up default permission groups
	    $defaultPermissionGroups=array(
	        'Moderator' => array(
	            'pages_access',
				'pages_add',
				'pages_edit',
				'pages_delete',
				'pages_publish'
	        ),
	        'Writer' => array(
	            'pages_access',
				'pages_add',
				'pages_edit',
				'pages_delete',
				'pages_publish'
	        )
	    );
	    foreach($defaultPermissionGroups as $groupName => $permissions) {
	        foreach($permissions as $permissionName) {
	            $statement=$db->prepare('addPermissionByGroupName');
	            $statement->execute(
	                array(
	                    ':groupName' => $groupName,
	                    ':permissionName' => $permissionName,
	                    ':value' => '0'
	                )
	            );
	        }
	    }
		if($db->countRows('pages_'.$lang)==0){
			$pages=array(
				'makeRegistrationAgreement' => 'Registration Agreement',
				'makeRegistrationEMail'     => 'Registration Email',
				'makeHomePage'              => 'Home',
			);
			foreach($pages as $queryName=>$humanName){
				echo '<h3>Attempting:</h3>',PHP_EOL;
				$db->exec($queryName,'installer',array('!lang!'=>$lang));
				echo '<div>',$humanName,' page installed!</div><br>',PHP_EOL;
			}
		}else{
			echo '<p class="exists">"pages database" already contains records</p>';
		}
	}
}
function pages_uninstall($db,$lang='en_us') {
    $db->dropTable('pages',$lang);
    $db->dropTable('pages_sidebars');
}
?>