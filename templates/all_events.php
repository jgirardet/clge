<?php
/**
 * Template variables:
 *
 * @var array<object{debut: DateTime, fin: DateTime, nom: string, abrev: string, lieu_physique: string, url: string, id: int}> $calEvents Liste des événements
 */
?>
<table id="cal_events_list" class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Début</th>
                    <th>Fin</th>
                    <th>Nom</th>
                    <th>Abrev</th>
                    <th>Lieu</th>
                    <th>URL</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    
                </tr>
    <?php
    
    if (!empty($calEvents)) :
        foreach ($calEvents as $event) : ?>
            <tr>
                        <td><?php echo esc_html($event->debut->format("d/m/Y H:i")); ?></td>
                        <td><?php echo esc_html($event->fin->format("d/m/Y H:i")); ?></td>
                        <td><strong><?php echo esc_html($event->nom); ?></strong></td>
                        <td><?php if (!empty($event->abrev)): ?>
                                <code><?php echo esc_html($event->abrev); ?></code>
                            <?php endif; ?></td>
                        <td>
                            <?php echo esc_html($event->lieu_physique); ?>
                            
                        </td>
                        <td>
                            <a href="<?php echo esc_url($event->url); ?>" target="_blank" rel="noopener">Voir</a>
                        </td>
                        <td>
                            <button hx-post="/wp-admin/admin-ajax.php" hx-trigger="click" hx-vals='{"action": "clge_delete_event", "event_id":"<?php echo $event->id ?>", "_wpnonce": "<?php echo wp_create_nonce('clge_delete_event'); ?>"}'
                            hx-target="#cal_events_list"
                        
                        >Supprimer</button></td>
                    </tr>
        <?php endforeach;
    else : ?>
        <tr>Aucun événement trouvé.</tr>
    <?php endif; ?>
            </tbody>
</table>