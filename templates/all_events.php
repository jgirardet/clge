<?php
/**
 * Template variables:
 *
 * @var array<object{debut: DateTime, fin: DateTime, nom: string, abrev: string, alias: string|null, description: string|null, lieu_physique: string, url: string, evt_clge: int, id: int}> $calEvents Liste des événements
 */

$delete_nonce = wp_create_nonce("clge_delete_event");
$update_nonce = wp_create_nonce("clge_update_event");
?>

<div id="cal_events_list">
	<!-- Header row -->
	<div class="clge-events-row clge-events-header">
		<div class="clge-events-row-col clge-th">Début</div>
		<div class="clge-events-row-col clge-th">Fin</div>
		<div class="clge-events-row-col clge-events-row-col--2x clge-th">Nom</div>
		<div class="clge-events-row-col clge-th">Abrév.</div>
		<div class="clge-events-row-col clge-th">Alias</div>
		<div class="clge-events-row-col clge-th">Lieu</div>
		<div class="clge-events-row-col clge-th">URL</div>
		<div class="clge-events-row-col clge-th">Evenement CLGE ?</div>
		<div class="clge-events-row-col clge-events-row-col--actions"></div>
	</div>

	<?php if (!empty($calEvents)): ?>
		<?php foreach ($calEvents as $event): ?>
			<div class="clge-events-row">
				<!-- Debut -->
				<div class="clge-events-row-col">
					<span class="clge-cell"><?php echo esc_html(
         $event->debut->format("d/m/Y H:i"),
     ); ?></span>
				</div>

				<!-- Fin -->
				<div class="clge-events-row-col">
					<span class="clge-cell"><?php echo esc_html(
         $event->fin->format("d/m/Y H:i"),
     ); ?></span>
				</div>

				<!-- Nom -->
				<div class="clge-events-row-col clge-events-row-col--2x">
					<span class="clge-cell clge-fw-bold"><?php echo esc_html($event->nom); ?></span>
				</div>

				<!-- Abreviation -->
				<div class="clge-events-row-col">
					<?php if (!empty($event->abrev)): ?>
						<span class="clge-badge clge-badge-abrev"><?php echo esc_html(
          $event->abrev,
      ); ?></span>
					<?php else: ?>
						<span class="clge-dash">—</span>
					<?php endif; ?>
				</div>

				<!-- Lieu -->
				<div class="clge-events-row-col">
					<span class="clge-cell"><?php echo esc_html($event->lieu_physique); ?></span>
				</div>

				<!-- URL -->
				<div class="clge-events-row-col">
					<?php if (!empty($event->url)): ?>
						<a href="<?php echo esc_url(
          $event->url,
      ); ?>" target="_blank" class="clge-link-btn">Voir</a>
					<?php else: ?>
						<span class="clge-dash">—</span>
					<?php endif; ?>
				</div>

				<!-- CLGE ? -->
				<div class="clge-events-row-col">
					<?php if (!empty($event->evt_clge)): ?>
						<span class="clge-badge clge-badge-success">✅</span>
					<?php endif; ?>
				</div>

				<!-- Delete button -->
				<div class="clge-events-row-col clge-events-row-col--actions">
					<button
						class="clge-btn clge-btn-danger"
						type="button"
						onmouseover="this.style.background='#b91c1c'"
						onmouseout="this.style.background='#dc2626'"
						hx-post="/wp-admin/admin-ajax.php"
						hx-trigger="click"
						hx-vals='{"action":"clge_delete_event","event_id":"<?php echo esc_attr(
          (string) absint($event->id),
      ); ?>","_wpnonce":"<?php echo esc_attr($delete_nonce); ?>"}'
						hx-target="#cal_events_list"
					>
						🗑
					</button>
				</div>
				<!-- Alias -->
				<div class="clge-events-row-col clge-events-row-col--full">
					<input
						type="text"
						name="alias"
						value="<?php echo esc_attr($event->alias ?? ""); ?>"
						placeholder="alias"
						class="clge-input"
						hx-post="/wp-admin/admin-ajax.php"
						hx-trigger="input delay:3s, change"
						hx-vals='{"action":"clge_update_event","event_id":"<?php echo esc_attr(
          (string) absint($event->id),
      ); ?>","_wpnonce":"<?php echo esc_attr($update_nonce); ?>"}'
						hx-target="#cal_events_list"
						hx-include="this"
						onkeydown="if(event.key === 'Enter') { event.preventDefault(); this.dispatchEvent(new Event('change')); }"
					/>
				</div>
				<div class="clge-events-row-col clge-events-row-col--full">
                        <textarea
                            name="description"
                            placeholder="Ajouter une description..."
                            class="clge-textarea"
                            hx-post="/wp-admin/admin-ajax.php"
                            hx-trigger="input delay:2s, change"
                            hx-vals='{"action":"clge_update_event_description","event_id":"<?php echo esc_attr(
                                (string) absint($event->id),
                            ); ?>","_wpnonce":"<?php echo esc_attr(
    wp_create_nonce("clge_update_event_description"),
); ?>"}'
                            hx-target="#cal_events_list"
                            hx-include="this"
                            onkeydown="if(event.key === 'Enter' && event.ctrlKey) { event.preventDefault(); this.dispatchEvent(new Event('change')); }"
                        ><?php echo esc_textarea(
                            $event->description ?? "",
                        ); ?></textarea>
					</div>
			</div>
		<?php endforeach; ?>
	<?php else: ?>
		<div class="clge-empty">Aucun événement trouvé.</div>
	<?php endif; ?>
</div>
