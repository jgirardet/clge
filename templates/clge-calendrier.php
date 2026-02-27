<div class="wrap">

    <style>
        h1 {
            text-align: center;
        }

        .add-form-elmts {
            color: grey;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        h2 {
            margin-top: 50px;
        }
    </style>




    <h1>Calendrier CLGE</h1>

    <?php if (!empty($error)): ?>
        <div class="notice notice-error">
            <p><?php echo esc_html($error); ?></p>
        </div>
    <?php elseif (!empty($message)): ?>
        <div class="notice notice-warning">
            <p><?php echo esc_html($message); ?></p>
        </div>
    <?php endif; ?>


    <h2>Ajouter un événement</h2>

    <form hx-post="/wp-admin/admin-ajax.php"
        hx-target="#cal_events_list"
        hx-swap="innerHTML"
        class="clge-add-form"
        hx-on::after-request="if(event.detail.successful) this.reset()"
        style="width: 90%;">
        <input type="hidden" name="action" value="clge_add_event">
        <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('clge_add_event'); ?>">


        <div style="width: 100%; margin: 0 auto; display: flex; gap: 15px;">
            <!-- Nom -->
            <div style="flex: 1; min-width: 120px;" class="add-form-elmts">
                <label for="nom" style="display: block; font-weight: bold; margin-bottom: 4px;">Nom*</label>
                <input type="text" id="nom" name="nom" required style="width: 100%; padding: 6px; box-sizing: border-box;">
            </div>

   
            <!-- Lieu -->
            <div style="flex: 1; min-width: 120px;" class="add-form-elmts">
                <label for="lieu_physique" style="display: block; font-weight: bold; margin-bottom: 4px;">Lieu</label>
                <input type="text" id="lieu_physique" name="lieu_physique" style="width: 100%; padding: 6px; box-sizing: border-box;">
            </div>

            <!-- URL -->
            <div style="flex: 1; min-width: 150px;" class="add-form-elmts">
                <label for="url" style="display: block; font-weight: bold; margin-bottom: 4px;">URL</label>
                <input type="text" id="url" name="url" style="width: 100%; padding: 6px; box-sizing: border-box;">
            </div>

            <!-- Date début -->
            <div class="add-form-elmts">
                <label for="debut" style="display: block; font-weight: bold; margin-bottom: 4px;">Date début*</label>
                <input type="date" id="debut" name="debut" required style="width: 100%; padding: 6px; box-sizing: border-box;"
                    _="on change set #date-fin's value to me.value">
            </div>

            <!-- Heure début -->
            <div class="add-form-elmts">
                <label for="debut_h" style="display: block; font-weight: bold; margin-bottom: 4px;">Heure</label>
                <input type="time" id="debut_h" name="debut_h" value="00:00" style="width: 100%; padding: 6px; box-sizing: border-box;"
                    _="on change set #heure-fin's value to me.value">
            </div>

            <!-- Date fin -->
            <div class="add-form-elmts">
                <label for="date-fin" style="display: block; font-weight: bold; margin-bottom: 4px;">Date fin</label>
                <input type="date" id="date-fin" name="fin" style="width: 100%; padding: 6px; box-sizing: border-box;">
            </div>

            <!-- Heure fin -->
            <div class="add-form-elmts">
                <label for="fin_h" style="display: block; font-weight: bold; margin-bottom: 4px;">Heure fin</label>
                <input id="heure-fin" type="time" id="fin_h" name="fin_h" value="00:00" style="width: 100%; padding: 6px; box-sizing: border-box;">
            </div>

            <button type="submit" style="flex: 1;" class=".add-form-elmts">Ajouter</button>
        </div>

    </form>


    <h2>Ajouter un événement CNGE</h2>

    <!-- , values: {action: select_cnge_formation_list} -->

    <div>
        <form hx-post="/wp-admin/admin-ajax.php" hx-target="#cal_events_list">
            <input type="hidden" name="action" value="clge_add_cnge_formation">
            <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('clge_add_cnge_formation'); ?>">
            <select id="cnge_formations" name="cnge" style="max-width:40rem;padding: 8px;" _="
        on load  htmx.ajax('GET', '/wp-admin/admin-ajax.php?action=select_cnge_formation_list', {target: '#cnge_formations'})
        ">
                <option value="" id="option-def">Chargement...</option>
            </select>
            <input type="submit">
        </form>
    </div>

    <h2>Liste des événements</h2>

    <div id="events-list" hx-get="<?php echo admin_url('admin-ajax.php'); ?>"
        hx-target="#events-list"
        hx-trigger="load"
        hx-swap="innerHTML"
        hx-vals='{"action": "clge_all_events"}'>

    </div>

</div>