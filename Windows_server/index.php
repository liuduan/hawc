<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php header("Access-Control-Allow-Origin: *");?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Bitnami: Open Source. Simplified</title>
<link href="bitnami.css" media="all" rel="Stylesheet" type="text/css" />
</head>
<body>
Hello, everyone.

<?php

// Show all information, defaults to INFO_ALL
phpinfo();

// Show just the module information.
// phpinfo(8) yields identical results.
phpinfo(INFO_MODULES);

?>

<div id="container">
  <div id="header"> 
    <div id="bitnami">
        <a href="/"><img alt="Bitnami" src="img/bitnami.png?1186088387" /></a>
    </div>
  </div>
    <div id="menu_launch_page">
      <table cellpadding="0" cellspacing="0">
        <tr>
          <td><img src="img/tab1_welcome.png" alt="" /></td>
          <td><a href="applications.html"><img src="img/tab2_welcome.png" alt="" /></a></td>
        </tr>
      </table>
    </div>
  <div id="lowerContainer">
    <div id="content">
        <div align="center">
<table class="tableParagraph">
<tr>
<td class="container">
<img align="left" src="img/wampstack.png" alt="Bitnami WAMP Stack">
<p>We created the Bitnami Project to help spread the adoption of freely
available, high quality Open Source web applications. Bitnami aims to make
it easier than ever to discover, download and install Open Source software such 
as document and content management systems, wikis and blogging software.<br/><br/>

You can learn more about Bitnami at <a href="https://bitnami.com">https://bitnami.com</a><br/><br/>

The Bitnami WAMP Stack is an easy to install
software platform that greatly simplifies the deployment of Open Source web
stacks. It includes ready-to-run versions of
Apache, MySQL and PHP. Bitnami WAMP Stack is
distributed for free under the Apache 2.0 license.<br/><br/> 
To get started with Bitnami WAMP Stack we suggest the following:<br/><br/>


<b>1.- <a target="_blank" href="https://wiki.bitnami.com/Infrastructure_Stacks/Bitnami_AMP_Stacks#What_is_the_default_installation_directory.3f">Check your installation.</a></b> The stack is self-contained and independent on your system, you can find all components in your installation directory: C:/Bitnami/wampstack-5.5.31-0<br/><br/>
<b>2.- <a target="_blank" href="https://wiki.bitnami.com/Infrastructure_Stacks/Bitnami_AMP_Stacks#How_do_I_start_and_stop_the_servers.3f">Start the servers.</a></b> Open the graphical "Manager" tool in your installation directory to start & stop the installed servers. You can also use "ctlscript.sh" from the command line prompt. <br/><br/>
<b>3.- <a target="_blank" href="https://wiki.bitnami.com/Infrastructure_Stacks/Bitnami_AMP_Stacks#How_can_I_add_applications_on_top_of_AMP.3f">Add more apps.</a></b> Download and install any Bitnami application module to run on top of this Stack.<br/><br/>
<b>4.- <a target="_blank" href="https://wiki.bitnami.com/Infrastructure_Stacks/Bitnami_AMP_Stacks#How_can_I_create_a_custom_PHP_application.3f">Deploy your own project.</a></b> Check our examples in the <a href="https://wiki.bitnami.com/Infrastructure_Stacks/Bitnami_AMP_Stacks">Quick Start Guide.</a><br/><br/>
</td>
</tr>
</table>
        </div>
        <br/><br/>
   </div>
  </div>
</div>
</body>
</html>
