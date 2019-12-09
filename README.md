# AdventureWorksProject

Component Versions

<b>Codeigniter</b> 3.1.8

<b>PHP</b> 7.1.x

<b>PostgreSQL</b> 9.5.12
<hr>

<p>1.) Updated default AdventureWorks PostgreSQL database.</p>
<p>2.) Run the sql file located in the <b><i>"AdventureWorksProject / scripts / update.sql"</i></b> directory.</p>
<p>3.) Complete your database configuration using the file in <b><i>"AdventureWorksProject / application / config / database.php"</i></b></p>
<p>4.) Make sure you complete the following settings in the <b><i>"AdventureWorksProject / application / config / library_config.php"</i></b> file (This step required to send mail notification);</p>
<ul>
<li><b>EMAIL_SMTP_HOST : </b>Your mail server provider's ip address or domain address</li>
<li><b>EMAIL_SMTP_PORT : </b>Mail server port</li>
<li><b>EMAIL_SMTP_USER : </b>Mail server username for auth if available</li>
<li><b>EMAIL_SMTP_PASSWORD : </b>Mail server password for auth if available</li>
<li><b>EMAIL_SENDER : </b>Sender name of mail server</li>
<li><b>EMAIL_SMTP_CRYPTO : </b>Mail sending method. (1=SSL, 2=TLS)</li>
</ul>
<p>5.) İmport the following xml file for mail notification. (It will run the .bat file);</p>
<ul>
<li><i>AdventureWorksProject/cronjobs/ADVENTURE_WORKS_EMAIL.xml</i></li>
</ul>
<p>6.) Change the following row in <b><i>"AdventureWorksProject/web.config"</i></b> file;</p>
<action type="Rewrite" url="/YOUR_PROJECT_MAIN_FILENAME/index.php/{R:1}"  />