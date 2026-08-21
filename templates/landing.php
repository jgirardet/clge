<script src="https://unpkg.com/htmx.org@2.0.2" integrity="sha384-Y7hw+L/jvKeWIRRkqWYfPcvVxHzVzn5REgzbawhxAuQGwX1XWe70vji+VSeHOThJ" crossorigin="anonymous"></script>
<script src="https://unpkg.com/hyperscript.org@0.9.14"></script>

<div class="wrap" id="landing-clge">

    <nav id="clge-navigation">
        <ul>
            <li><a href="#calendrier" hx-get="/wp-admin/admin-ajax.php?action=clge_calendrier" hx-target="#clge-content" hx-push-url="true">Calendrier</a></li>
            <li><a href="#nextcloud" hx-get="/wp-admin/admin-ajax.php?action=clge_nextcloud_settings" hx-target="#clge-content" hx-push-url="true">Nextcloud</a></li>
            <li><a href="#debug" hx-get="/wp-admin/admin-ajax.php?action=clge_debug_page" hx-target="#clge-content" hx-push-url="true">Debug</a></li>
            <li><a href="#services">Autres</a></li>
        </ul>
    </nav>
    <div id="clge-content" hx-get="/wp-admin/admin-ajax.php?action=clge_calendrier" hx-trigger="load">

    </div>
</div>
