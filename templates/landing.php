<script src="https://unpkg.com/htmx.org@2.0.2" integrity="sha384-Y7hw+L/jvKeWIRRkqWYfPcvVxHzVzn5REgzbawhxAuQGwX1XWe70vji+VSeHOThJ" crossorigin="anonymous"></script>
<script src="https://unpkg.com/hyperscript.org@0.9.14"></script>

<style>
    :root {
        --clge-font-size-base: 16px;
        --clge-font-size-large: 18px;
        --clge-font-size-heading: 20px;
    }

    body {
        font-size: var(--clge-font-size-base) !important;
        line-height: 1.6 !important;
    }

    h1, h2, h3, h4, h5, h6 {
        font-size: var(--clge-font-size-heading) !important;
        font-weight: 600 !important;
        margin-bottom: 1rem !important;
    }

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
        font-size: var(--clge-font-size-large) !important;
        font-weight: 500 !important;
    }

    #clge-navigation li a:hover {
        background-color: #111;
    }

    .clge-events-row {
        font-size: var(--clge-font-size-base) !important;
    }

    .clge-date, .clge-name, .clge-location {
        font-size: 14px !important;
    }

    button, a {
        font-size: var(--clge-font-size-base) !important;
    }

    /* Amélioration des espacements */
    .wrap {
        padding: 20px !important;
    }

    #clge-content {
        margin-top: 20px !important;
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