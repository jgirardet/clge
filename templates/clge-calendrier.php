<?php
/**
 * @var string|null $error
 * @var string|null $message
 */
?>
<div class="wrap clge-calendar-page">


	<h1>Calendrier CLGE</h1>

	<div class="clge-notice-wrap">
		<?php if (!empty($error)): ?>
			<div class="notice notice-error">
				<p><?php echo esc_html($error); ?></p>
			</div>
		<?php elseif (!empty($message)): ?>
			<div class="notice notice-warning">
				<p><?php echo esc_html($message); ?></p>
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
			<input type="hidden" name="_wpnonce" value="<?php echo esc_attr(
       wp_create_nonce("clge_add_event"),
   ); ?>">
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

			<div class="clge-field clge-mt-3">
				<label for="description">Description</label>
				<textarea id="description" name="description" rows="4"></textarea>
			</div>

			<div class="clge-mt-3">
				<button type="submit" class="clge-submit">Ajouter</button>
			</div>
		</form>
	</section>

	<section class="clge-card">
		<h2>Ajouter un événement CNGE</h2>
		<p class="clge-muted">Importe rapidement une formation CNGE depuis la liste synchronisée.</p>

		<form hx-post="/wp-admin/admin-ajax.php" hx-target="#cal_events_list">
			<input type="hidden" name="action" value="clge_add_cnge_formation">
			<input type="hidden" name="_wpnonce" value="<?php echo esc_attr(
       wp_create_nonce("clge_add_cnge_formation"),
   ); ?>">

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
			hx-get="<?php echo esc_url(admin_url("admin-ajax.php")); ?>"
			hx-target="#events-list"
			hx-trigger="load"
			hx-swap="innerHTML"
			hx-vals='{"action":"clge_all_events"}'
		>
			<div class="clge-loading">Chargement des événements...</div>
		</div>
	</section>

</div>
