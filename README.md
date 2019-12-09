# AdventureWorksProject

Component Versions

<b>Codeigniter</b> 3.1.8

<b>PHP</b> 7.1.x

<b>PostgreSQL</b> 9.5.12

<p>Updated default AdventureWorks PostgreSQL database.</p>
<p>Run the sql file located in the <b><i>"AdventureWorksProject / scripts / update.sql"</i></b> directory.</p>
<p>Complete your database configuration using the file in <b><i>"AdventureWorksProject / application / config / database.php"</i></b></p>
<p>Make sure you complete the following settings in the <b><i>"AdventureWorksProject / application / config / library_config.php"</i></b> file (This step required to send mail notification);</p>
<ul>
<li>EMAIL_SMTP_HOST : Your mail server provider's ip address or domain address</li>
<li>EMAIL_SMTP_PORT : Mail server port</li>
<li>EMAIL_SMTP_USER: Mail server username for auth if available</li>
<li>EMAIL_SMTP_PASSWORD : Mail server password for auth if available</li>
<li>EMAIL_SENDER : Sender name of mail server</li>
<li>EMAIL_SMTP_CRYPTO : Mail sending method. (1=SSL, 2=TLS)</li>
</ul>
<p>İmport the following xml file for mail notification. (It will run the .bat file.);</p>
<ul>
<li><i>AdventureWorksProject/cronjobs/ADVENTURE_WORKS_EMAIL.xml</i></li>
</ul>