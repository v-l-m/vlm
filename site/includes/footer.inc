    </div> <!-- fin div main --> 
  </div> <!-- fin div contenaire -->

  <div id="footer">
    <!--
      <p>
        <a href="http://validator.w3.org/check?uri=referer">
          <img src="http://www.w3.org/Icons/valid-xhtml10" title ="Valid XHTML 1.0!" alt="Valid XHTML 1.0!" height="31" width="88" />
        </a>
        <a href="http://www.spreadfirefox.com/?q=affiliates&amp;id=0&amp;t=70"><img alt="Get Firefox!" title="Get Firefox!" src="http://sfx-images.mozilla.org/affiliates/Buttons/88x31/get.gif"/></a>
      </p>
    -->
  </div> <!--footer-->
	<!-- TIMINGS: <?php if(isset($page_start_time)) echo "generated in ".(microtime(1)-$page_start_time)."s"; ?> -->
</body>
<?php
  if (isPlayerLoggedIn() && VLM_XMPP_ON) {
    if (isSecure())
    {
      $proto = "https://";
    }
    else
    {
      $proto = "http://";
    }
?>
<script type="text/javascript">
require(['converse'], function (converse) {
    converse.initialize({
        i18n: locales['<?echo getCurrentLang();?>'], // Refer to ./locale/locales.js to see which locales are supported
        jid: '<?echo getLoggedPlayerObject()->getFullJid(); ?>',
        roster_groups: true,
        allow_otr : false, //pour éviter les problèmes inter-clients ?
        allow_registration : false, // (pour mémoire)
        auto_list_rooms : true, // (pour afficher les chat rooms de races, notamment)
        auto_subscribe : true, // (pour épargner un clic aux joueurs qui pourront toujours blacklister par la suite ?)
        bosh_service_url : '<?echo $proto.VLM_XMPP_HTTP_BIND_URL ?>', //Obligatoire pour que ça marche
        message_carbons : true, // pour permettre de chatter indifférement et en meme temps dans les outils et dans le site
      	keepalive : true,
        prebind : true,
        prebind_url : '/xmpp-pre-bind.php', //Pour maintenir la session
        show_controlbox_by_default : false, // Ne pas encombrer l'écran par défaut
        hide_muc_server : true,
        xhr_user_search :true,
        xhr_user_search_url : '/ws/playerinfo/list-converse.php', //Pour avoir un annuaire des joueurs (ticket #798)
        allow_logout: false
    });
});
</script>
<?php
}
?>
</html>
<?php

  mysql_close();

?>
