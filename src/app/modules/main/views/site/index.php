<h2>I'm view, find me in: app/modules/main/views/site/index.php</h2>
<p><?= $message ?></p>
<p><a href="<?= $this->createUrl('about') ?>">About</a></p>
<p><a href="<?= $this->createUrl('greeting', array('name' => 'guest')) ?>">Greeting</a></p>
