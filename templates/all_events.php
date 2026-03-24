<?php
/**
 * Template variables:
 *
 * @var array<object{debut: DateTime, fin: DateTime, nom: string, abrev: string, lieu_physique: string, url: string, evt_clge: int, id: int}> $calEvents Liste des événements
 */

$delete_nonce = wp_create_nonce( 'clge_delete_event' );
?>

<style>
	/* ====== CLGE Events List (Flex layout) ====== */
	.clge-events-wrap {
		--clge-bg: #ffffff;
		--clge-border: #e5e7eb;
		--clge-header-bg: #f8fafc;
		--clge-header-text: #0f172a;
		--clge-row-hover: #f8fafc;
		--clge-text: #1f2937;
		--clge-muted: #6b7280;
		--clge-accent: #2563eb;
		--clge-danger: #dc2626;
		--clge-danger-hover: #b91c1c;
		--clge-success-bg: #dcfce7;
		--clge-success-text: #166534;
		--clge-error-bg: #fee2e2;
		--clge-error-text: #991b1b;
		--clge-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);

		margin-top: 1rem;
		border: 1px solid var(--clge-border);
		border-radius: 12px;
		overflow: hidden;
		background: var(--clge-bg);
		box-shadow: var(--clge-shadow);
		color: var(--clge-text);
		font-size: 14px;
	}

	.clge-events-row {
		display: flex;
		align-items: stretch;
		gap: 10px;
		padding: 10px 12px;
		border-bottom: 1px solid var(--clge-border);
	}

	.clge-events-row:last-child {
		border-bottom: none;
	}

	.clge-events-row:nth-child(even) {
		background: #fcfcfd;
	}

	.clge-events-row:hover {
		background: var(--clge-row-hover);
	}

	.clge-events-header {
		background: var(--clge-header-bg);
		color: var(--clge-header-text);
		font-weight: 600;
		position: sticky;
		top: 0;
		z-index: 1;
	}

	.clge-col {
		min-width: 0;
		display: flex;
		align-items: center;
	}

	/* Colonnes compactes + bases fixes pour aligner header et lignes */
	.clge-col--debut {
		flex: 0 0 165px;
		justify-content: flex-start;
		white-space: nowrap;
	}

	.clge-col--fin {
		flex: 0 0 165px;
		justify-content: flex-start;
		white-space: nowrap;
	}

	.clge-col--abrev {
		flex: 1 1 80px;
		justify-content: center;
		white-space: nowrap;
	}

	.clge-col--url {
		flex: 1 1 80px;
		justify-content: center;
		white-space: nowrap;
	}

	.clge-col--clge {
		flex: 1 1 80px;
		justify-content: center;
		white-space: nowrap;
	}

	.clge-col--action {
		flex: 0 0 40px;
		justify-content: center;
		white-space: nowrap;
	}

	/* Colonnes fluides */
	.clge-col--nom {
		flex: 2.8 1 520px;
		justify-content: center;
		text-align: center;
	}

	.clge-col--lieu {
		flex: 1 1 160px;
		justify-content: flex-start;
	}

	.clge-label {
		font-size: 11px;
		font-weight: 600;
		color: #64748b;
		text-transform: uppercase;
		letter-spacing: 0.04em;
		display: none;
		margin-bottom: 4px;
	}

	.clge-events-header .clge-col {
		font-size: 13px;
		font-weight: 600;
		white-space: nowrap;
	}

	.clge-events-header .clge-col--debut,
	.clge-events-header .clge-col--fin,
	.clge-events-header .clge-col--lieu {
		justify-content: flex-start;
	}

	.clge-events-header .clge-col--nom,
	.clge-events-header .clge-col--abrev,
	.clge-events-header .clge-col--url,
	.clge-events-header .clge-col--clge,
	.clge-events-header .clge-col--action {
		justify-content: center;
	}

	.clge-date {
		display: inline-flex;
		align-items: center;
		gap: 6px;
		white-space: nowrap;
		font-variant-numeric: tabular-nums;
		font-weight: 600;
		color: #0f172a;
		background: #f8fafc;
		border: 1px solid #e2e8f0;
		border-radius: 8px;
		padding: 5px 9px;
	}

	.clge-date.is-start::before {
		content: "🟢";
		font-size: 12px;
	}

	.clge-date.is-end::before {
		content: "🔴";
		font-size: 12px;
	}

	.clge-name {
		display: inline-block;
		font-weight: 700;
		letter-spacing: 0.1px;
		color: #0b1220;
		background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
		border: 1px solid #e2e8f0;
		border-radius: 8px;
		padding: 6px 10px;
		max-width: 100%;
		white-space: normal;
		overflow-wrap: anywhere;
		word-break: break-word;
		text-align: center;
	}

	.clge-location {
		display: inline-block;
		color: #334155;
		background: #f8fafc;
		border: 1px solid #e2e8f0;
		border-radius: 8px;
		padding: 6px 10px;
		max-width: 100%;
		white-space: normal;
		overflow-wrap: anywhere;
		word-break: break-word;
	}

	.clge-abrev {
		display: inline-block;
		padding: 1px 6px;
		border-radius: 6px;
		background: #f1f5f9;
		color: #334155;
		font-size: 11px;
		line-height: 1.2;
		font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
	}

	.clge-link {
		color: var(--clge-accent);
		text-decoration: none;
		font-weight: 500;
	}

	.clge-link:hover,
	.clge-link:focus {
		text-decoration: underline;
	}

	.clge-badge {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		padding: 2px 7px;
		border-radius: 999px;
		font-size: 11px;
		font-weight: 600;
		white-space: nowrap;
		min-width: 28px;
	}

	.clge-badge.yes {
		background: var(--clge-success-bg);
		color: var(--clge-success-text);
	}

	.clge-badge.no {
		background: var(--clge-error-bg);
		color: var(--clge-error-text);
	}

	.clge-btn-delete {
		border: 0;
		background: var(--clge-danger);
		color: #fff;
		padding: 5px 8px;
		border-radius: 8px;
		font-size: 11px;
		font-weight: 600;
		cursor: pointer;
		transition: background 0.2s ease;
		white-space: nowrap;
	}

	.clge-btn-delete:hover,
	.clge-btn-delete:focus {
		background: var(--clge-danger-hover);
	}

	.clge-empty {
		padding: 20px;
		text-align: center;
		color: var(--clge-muted);
		font-style: italic;
	}

	/* Responsive: on mobile, stack each row */
	@media (max-width: 920px) {
		.clge-events-header {
			display: none;
		}

		.clge-events-row {
			flex-wrap: wrap;
			gap: 8px 10px;
		}

		.clge-col {
			flex: 1 1 100%;
			align-items: flex-start;
		}

		.clge-col--debut,
		.clge-col--fin,
		.clge-col--abrev,
		.clge-col--url,
		.clge-col--clge,
		.clge-col--action {
			flex: 0 1 auto;
		}

		.clge-col--nom,
		.clge-col--lieu {
			flex: 1 1 100%;
			justify-content: flex-start;
			text-align: left;
		}

		.clge-name {
			text-align: left;
		}

		.clge-label {
			display: block;
		}
	}
</style>

<div id="cal_events_list" class="clge-events-wrap">
	<div class="clge-events-row clge-events-header" aria-hidden="true">
		<div class="clge-col clge-col--debut">Début</div>
		<div class="clge-col clge-col--fin">Fin</div>
		<div class="clge-col clge-col--nom">Nom</div>
		<div class="clge-col clge-col--abrev">Abrév.</div>
		<div class="clge-col clge-col--lieu">Lieu</div>
		<div class="clge-col clge-col--url">URL</div>
		<div class="clge-col clge-col--clge">Evenement CLGE ?</div>
		<div class="clge-col clge-col--action"></div>
	</div>

	<?php if ( ! empty( $calEvents ) ) : ?>
		<?php foreach ( $calEvents as $event ) : ?>
			<div class="clge-events-row">
				<div class="clge-col clge-col--debut">
					<div>
						<span class="clge-label">Début</span>
						<span class="clge-date is-start"><?php echo esc_html( $event->debut->format( 'd/m/Y H:i' ) ); ?></span>
					</div>
				</div>

				<div class="clge-col clge-col--fin">
					<div>
						<span class="clge-label">Fin</span>
						<span class="clge-date is-end"><?php echo esc_html( $event->fin->format( 'd/m/Y H:i' ) ); ?></span>
					</div>
				</div>

				<div class="clge-col clge-col--nom">
					<div>
						<span class="clge-label">Nom</span>
						<span class="clge-name"><?php echo esc_html( $event->nom ); ?></span>
					</div>
				</div>

				<div class="clge-col clge-col--abrev">
					<div>
						<span class="clge-label">Abrév.</span>
						<?php if ( ! empty( $event->abrev ) ) : ?>
							<span class="clge-abrev"><?php echo esc_html( $event->abrev ); ?></span>
						<?php else : ?>
							<span aria-hidden="true">—</span>
						<?php endif; ?>
					</div>
				</div>

				<div class="clge-col clge-col--lieu">
					<div>
						<span class="clge-label">Lieu</span>
						<span class="clge-location"><?php echo esc_html( $event->lieu_physique ); ?></span>
					</div>
				</div>

				<div class="clge-col clge-col--url">
					<div>
						<span class="clge-label">URL</span>
						<?php if ( ! empty( $event->url ) ) : ?>
							<a class="clge-link" href="<?php echo esc_url( $event->url ); ?>" target="_blank" rel="noopener noreferrer">Voir</a>
						<?php else : ?>
							<span aria-hidden="true">—</span>
						<?php endif; ?>
					</div>
				</div>

				<div class="clge-col clge-col--clge">
					<div>
						<span class="clge-label">CLGE ?</span>
						<?php if ( ! empty( $event->evt_clge ) ) : ?>
							<span class="clge-badge">✅</span>
						<?php else : ?>
							<span class=""></span>
						<?php endif; ?>
					</div>
				</div>

				<div class="clge-col clge-col--action">
					<div>
						<span class="clge-label">Action</span>
						<button
							class="clge-btn-delete"
							type="button"
							hx-post="/wp-admin/admin-ajax.php"
							hx-trigger="click"
							hx-vals='{"action":"clge_delete_event","event_id":"<?php echo esc_attr( (string) absint( $event->id ) ); ?>","_wpnonce":"<?php echo esc_attr( $delete_nonce ); ?>"}'
							hx-target="#cal_events_list"
						>
							🗑
						</button>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	<?php else : ?>
		<div class="clge-empty">Aucun événement trouvé.</div>
	<?php endif; ?>
</div>
