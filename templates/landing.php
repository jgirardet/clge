<script src="https://unpkg.com/htmx.org@2.0.2" integrity="sha384-Y7hw+L/jvKeWIRRkqWYfPcvVxHzVzn5REgzbawhxAuQGwX1XWe70vji+VSeHOThJ" crossorigin="anonymous"></script>
<script src="https://unpkg.com/hyperscript.org@0.9.14"></script>

<style>
    #clge-navigation {
        background-color: #333;
        overflow: hidden;
    }

    #clge-navigation ul {
        list-style-type: none;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: row;
    }



    #clge-navigation li a {
        display: block;
        color: white;
        text-align: center;
        padding: 14px 16px;
        text-decoration: none;
    }

    #clge-navigation li a:hover {
        background-color: #111;
    }
</style>
<div class="wrap" id="landing-clge">

    <nav id="clge-navigation">
        <ul>
            <li><a href="#calendrier">Calendrier</a></li>
            <li><a href="#services">Autres</a></li>
        </ul>
    </nav>
    <div id="clge-content" hx-get="/wp-admin/admin-ajax.php?action=clge_calendrier" hx-trigger="load">

    </div>
</div>