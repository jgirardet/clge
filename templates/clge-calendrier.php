<?php
/**
 * @var string|null $error
 * @var string|null $message
 */
?>
<div class="wrap clge-calendar-page">

	<style>
		.clge-calendar-page {
			--clge-bg: #ffffff;
			--clge-surface: #f8fafc;
			--clge-border: #e5e7eb;
			--clge-border-strong: #cbd5e1;
			--clge-text: #1f2937;
			--clge-muted: #6b7280;
			--clge-title: #0f172a;
			--clge-accent: #2563eb;
			--clge-accent-hover: #1d4ed8;
			--clge-success: #16a34a;
			--clge-danger: #dc2626;
			--clge-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
		}

		.clge-calendar-page h1 {
			margin: 12px 0 24px;
			text-align: center;
			color: var(--clge-title);
			font-size: 30px;
			letter-spacing: 0.2px;
		}

		.clge-calendar-page h2 {
			margin: 0 0 14px;
			color: var(--clge-title);
			font-size: 20px;
		}

		.clge-card {
			background: var(--clge-bg);
			border: 1px solid var(--clge-border);
			border-radius: 12px;
			box-shadow: var(--clge-shadow);
			padding: 18px;
			margin-bottom: 18px;
		}

		.clge-muted {
			color: var(--clge-muted);
			font-size: 13px;
			margin-top: -4px;
			margin-bottom: 12px;
		}

		.clge-notice-wrap .notice {
			border-radius: 8px;
			margin: 0 0 16px;
		}

		.clge-add-form {
			width: 100%;
		}

		.clge-form-grid {
			display: grid;
			grid-template-columns: 2fr 1.4fr 1.6fr 1fr 0.9fr 1fr 0.9fr auto;
			gap: 12px;
			align-items: end;
		}

		.clge-field {
			display: flex;
			flex-direction: column;
			gap: 6px;
			min-width: 0;
		}

		.clge-field label {
			font-size: 12px;
			font-weight: 600;
			color: #334155;
			letter-spacing: 0.03em;
		}

		.clge-field input,
		.clge-field select {
			width: 100%;
			height: 38px;
			padding: 8px 10px;
			border: 1px solid var(--clge-border-strong);
			border-radius: 8px;
			background: #fff;
			color: var(--clge-text);
			box-sizing: border-box;
			transition: border-color 0.15s ease, box-shadow 0.15s ease;
		}

		.clge-field input:focus,
		.clge-field select:focus {
			border-color: var(--clge-accent);
			outline: none;
			box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
		}

		.clge-submit {
			height: 38px;
			padding: 0 14px;
			border: 0;
			border-radius: 8px;
			background: var(--clge-accent);
			color: #fff;
			font-weight: 600;
			cursor: pointer;
			white-space: nowrap;
			transition: background 0.2s ease, transform 0.05s ease;
		}

		.clge-submit:hover,
		.clge-submit:focus {
			background: var(--clge-accent-hover);
		}

		.clge-submit:active {
			transform: translateY(1px);
		}

		.clge-submit.secondary {
			background: #0f766e;
		}

		.clge-submit.secondary:hover,
		.clge-submit.secondary:focus {
			background: #0d5f59;
		}

		.clge-cnge-row {
			display: flex;
			gap: 10px;
			align-items: end;
			flex-wrap: wrap;
		}

		.clge-cnge-row .clge-field {
			flex: 1 1 360px;
		}

		.clge-cnge-row .clge-submit {
			flex: 0 0 auto;
		}

		.clge-events-shell {
			background: var(--clge-surface);
			border: 1px solid var(--clge-border);
			border-radius: 12px;
			padding: 10px;
			min-height: 80px;
		}

		.clge-loading {
			color: var(--clge-muted);
			font-style: italic;
			padding: 8px;
		}

		@media (max-width: 1280px) {
			.clge-form-grid {
				grid-template-columns: 2fr 1.5fr 1.6fr 1fr 1fr 1fr 1fr;
			}

			.clge-form-grid .clge-submit {
				grid-column: 1 / -1;
				justify-self: start;
			}
		}

		@media (max-width: 860px) {
			.clge-calendar-page h1 {
				font-size: 25px;
			}

			.clge-form-grid {
				grid-template-columns: 1fr;
			}

			.clge-form-grid .clge-submit {
				width: 100%;
			}

			.clge-cnge-row .clge-submit {
				width: 100%;
			}
		}
	</style>

	<h1>Calendrier CLGE</h1>

	<div class="clge-notice-wrap">
		<?php if ( ! empty( $error ) ) : ?>
			<div class="notice notice-error">
				<p><?php echo esc_html( $error ); ?></p>
			</div>
		<?php elseif ( ! empty( $message ) ) : ?>
			<div class="notice notice-warning">
				<p><?php echo esc_html( $message ); ?></p>
			</div>
		<?php endif; ?>
	</div>

	<section class="clge-card">
		<h2>Ajouter un événement</h2>
		<p class="clge-muted">Crée un événement CLGE personnalisé. Les champs marqués d’un * sont obligatoires.</p>

		<form
			hx-post="/wp-admin/admin-ajax.php"
			hx-target="#cal_events_list"
			hx-swap="innerHTML"
			class="clge-add-form"
			hx-on::after-request="if(event.detail.successful) this.reset()"
		>
			<input type="hidden" name="action" value="clge_add_event">
			<input type="hidden" name="_wpnonce" value="<?php echo esc_attr( wp_create_nonce( 'clge_add_event' ) ); ?>">
			<input type="hidden" name="evt_clge" value="1">

			<div class="clge-form-grid">
				<div class="clge-field">
					<label for="nom">Nom*</label>
					<input type="text" id="nom" name="nom" required>
				</div>

				<div class="clge-field">
					<label for="lieu_physique">Lieu</label>
					<input type="text" id="lieu_physique" name="lieu_physique">
				</div>

				<div class="clge-field">
					<label for="url">URL</label>
					<input type="text" id="url" name="url">
				</div>

				<div class="clge-field">
					<label for="debut">Date début*</label>
					<input
						type="date"
						id="debut"
						name="debut"
						required
						_="on change set #date-fin's value to me.value"
					>
				</div>

				<div class="clge-field">
					<label for="debut_h">Heure</label>
					<input
						type="time"
						id="debut_h"
						name="debut_h"
						value="00:00"
						_="on change set #heure-fin's value to me.value"
					>
				</div>

				<div class="clge-field">
					<label for="date-fin">Date fin</label>
					<input type="date" id="date-fin" name="fin">
				</div>

				<div class="clge-field">
					<label for="heure-fin">Heure fin</label>
					<input type="time" id="heure-fin" name="fin_h" value="00:00">
				</div>
			</div>

			<div class="clge-field" style="margin-top: 12px;">
				<label for="description">Description</label>
				<textarea id="description" name="description" rows="4" style="resize: vertical; width: 100%;"></textarea>
			</div>

			<div style="margin-top: 12px;">
				<button type="submit" class="clge-submit">Ajouter</button>
			</div>
		</form>
	</section>

	<section class="clge-card">
		<h2>Ajouter un événement CNGE</h2>
		<p class="clge-muted">Importe rapidement une formation CNGE depuis la liste synchronisée.</p>

		<form hx-post="/wp-admin/admin-ajax.php" hx-target="#cal_events_list">
			<input type="hidden" name="action" value="clge_add_cnge_formation">
			<input type="hidden" name="_wpnonce" value="<?php echo esc_attr( wp_create_nonce( 'clge_add_cnge_formation' ) ); ?>">

			<div class="clge-cnge-row">
				<div class="clge-field">
					<label for="cnge_formations">Formation CNGE</label>
					<select
						id="cnge_formations"
						name="cnge"
						_="on load htmx.ajax('GET', '/wp-admin/admin-ajax.php?action=select_cnge_formation_list', {target: '#cnge_formations'})"
					>
						<option value="" id="option-def">Chargement...</option>
					</select>
				</div>

				<button type="submit" class="clge-submit secondary">Ajouter la formation</button>
			</div>
		</form>
	</section>

	<section class="clge-card">
		<h2>Liste des événements</h2>
		<div
			class="clge-events-shell"
			id="events-list"
			hx-get="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>"
			hx-target="#events-list"
			hx-trigger="load"
			hx-swap="innerHTML"
			hx-vals='{"action":"clge_all_events"}'
		>
			<div class="clge-loading">Chargement des événements...</div>
		</div>
	</section>

</div>