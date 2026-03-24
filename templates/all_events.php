<?php
/**
 * Template variables:
 *
 * @var array<object{debut: DateTime, fin: DateTime, nom: string, abrev: string, alias: string|null, description: string|null, lieu_physique: string, url: string, evt_clge: int, id: int}> $calEvents Liste des événements
 */

$delete_nonce = wp_create_nonce("clge_delete_event"); ?>

<style>
    .clge-events-wrap {
        margin-top: 1rem;
        font-size: 16px;
    }

	.clge-events-row {
		display: flex;
		flex-wrap: wrap;
		gap: 10px;
		padding: 10px;
		background: #f8fafc;
	}

	.clge-col {
		flex: 1 1 100%;
		min-width: 0;
	}

    .clge-date, .clge-name, .clge-location {
        padding: 8px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        background: white;
        font-size: 15px;
    }

    .clge-btn-delete {
        background: #dc2626;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
    }

    .clge-empty {
        padding: 20px;
        text-align: center;
        color: #6b7280;
        font-style: italic;
        font-size: 16px;
    }

	@media (max-width: 600px) {
		.clge-events-row {
			flex-direction: column;
		}
	}
</style>

<div id="cal_events_list">
	<div style="display: flex; flex-wrap: wrap; gap: 10px; padding: 10px; background: #f8fafc; font-weight: bold;">
        <div style="flex: 1; font-size: 15px; font-weight: 600;">Début</div>
        <div style="flex: 1; font-size: 15px; font-weight: 600;">Fin</div>
        <div style="flex: 2; font-size: 15px; font-weight: 600;">Nom</div>
        <div style="flex: 1; font-size: 15px; font-weight: 600;">Abrév.</div>
        <div style="flex: 1; font-size: 15px; font-weight: 600;">Alias</div>
        <div style="flex: 1; font-size: 15px; font-weight: 600;">Lieu</div>
        <div style="flex: 1; font-size: 15px; font-weight: 600;">URL</div>
        <div style="flex: 1; font-size: 15px; font-weight: 600;">Evenement CLGE ?</div>
        <div style="flex: 0 0 50px;"></div>
	</div>

	<?php if (!empty($calEvents)): ?>
		<?php foreach ($calEvents as $event): ?>
			<div style="display: flex; flex-wrap: wrap; gap: 12px; padding: 12px; background: #f8fafc; border-radius: 8px; margin-bottom: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); border: 1px solid #e2e8f0;">
				<div style="flex: 1; min-width: 120px;">
                    <span style="display: block; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 6px; background: white; font-size: 14px;"><?php echo esc_html($event->debut->format("d/m/Y H:i")); ?></span>
				</div>

				<div style="flex: 1; min-width: 120px;">
                    <span style="display: block; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 6px; background: white; font-size: 14px;"><?php echo esc_html($event->fin->format("d/m/Y H:i")); ?></span>
				</div>

				<div style="flex: 2; min-width: 200px;">
                    <span style="display: block; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 6px; background: white; font-size: 15px; font-weight: 600;"><?php echo esc_html($event->nom); ?></span>
				</div>

				<div style="flex: 1; min-width: 80px; text-align: center;">
					<?php if (!empty($event->abrev)): ?>
                        <span style="display: inline-block; padding: 4px 10px; border-radius: 4px; background: #f1f5f9; color: #334155; font-size: 13px; font-family: monospace;"><?php echo esc_html($event->abrev); ?></span>
					<?php else: ?>
						<span style="color: #9ca3af;">—</span>
					<?php endif; ?>
				</div>

				<div style="flex: 1; min-width: 80px; text-align: center;">
					<?php if (!empty($event->alias)): ?>
                        <span style="display: inline-block; padding: 4px 10px; border-radius: 4px; background: #e0e7ff; color: #3730a3; font-size: 13px; font-family: monospace;"><?php echo esc_html($event->alias); ?></span>
					<?php else: ?>
						<span style="color: #9ca3af;">—</span>
					<?php endif; ?>
				</div>

				<div style="flex: 1; min-width: 150px;">
                    <span style="display: block; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 6px; background: white; font-size: 14px;"><?php echo esc_html($event->lieu_physique); ?></span>
				</div>

				<div style="flex: 1; min-width: 80px; text-align: center;">
					<?php if (!empty($event->url)): ?>
                        <a href="<?php echo esc_url($event->url); ?>" target="_blank" style="display: inline-block; padding: 6px 12px; background: #dbeafe; color: #2563eb; border-radius: 4px; text-decoration: none; font-size: 13px; font-weight: 600;">Voir</a>
					<?php else: ?>
						<span style="color: #9ca3af;">—</span>
					<?php endif; ?>
				</div>

				<div style="flex: 1; min-width: 60px; text-align: center;">
					<?php if (!empty($event->evt_clge)): ?>
                        <span style="display: inline-block; padding: 3px 8px; background: #dcfce7; color: #166534; border-radius: 999px; font-size: 13px; font-weight: 600;">✅</span>
					<?php endif; ?>
				</div>

				<div style="flex: 0 0 50px; display: flex; align-items: center; justify-content: center;">
                    <button
                        style="background: #dc2626; color: white; border: none; padding: 8px 14px; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 600; transition: background 0.2s;"
                        type="button"
                        onmouseover="this.style.background='#b91c1c'"
                        onmouseout="this.style.background='#dc2626'"
                        hx-post="/wp-admin/admin-ajax.php"
                        hx-trigger="click"
                        hx-vals='{"action":"clge_delete_event","event_id":"<?php echo esc_attr((string) absint($event->id)); ?>","_wpnonce":"<?php echo esc_attr($delete_nonce); ?>"}'
                        hx-target="#cal_events_list"
                    >
                        🗑
                    </button>
				</div>
				<?php if (!empty($event->description)): ?>
					<div style="flex-basis: 100%; padding-top: 12px; margin-top: 8px;">
                        <p style="margin: 0; padding: 10px 14px; background: #f1f5f9; border-radius: 6px; color: #334155; font-size: 15px; line-height: 1.5;"><?php echo esc_html($event->description); ?></p>
					</div>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	<?php else: ?>
		<div style="padding: 20px; text-align: center; color: #6b7280; font-style: italic;">Aucun événement trouvé.</div>
	<?php endif; ?>
</div>
