(function () {
if (!window.OrgaPressPrivacy) {
return;
}

function getConsent() {
    try {
        const raw = document.cookie.split('; ').find(c => c.startsWith(OrgaPressPrivacy.cookie + '='));
        if (!raw) return {};
        return JSON.parse(decodeURIComponent(raw.split('=')[1]));
    } catch (e) {
        return {};
    }
}

function setConsent(state) {
    document.cookie =
        OrgaPressPrivacy.cookie +
        '=' +
        encodeURIComponent(JSON.stringify(state)) +
        ';path=/;max-age=' +
        60 * 60 * 24 * 365;

    if (window.jQuery) {
        jQuery.post(
            OrgaPressPrivacy.ajax_url,
            {
                action: 'orgapress_log_consent',
                nonce: OrgaPressPrivacy.nonce,
                consent: state
            }
        );
    }
}

function buildModal() {
    const labels = OrgaPressPrivacy.labels;
    // Banner Creation
    const banner = document.createElement('div');
    banner.id = 'orgapress-consent-banner';
    banner.innerHTML = `
        <div class="orgapress-consent-banner-text">${labels.banner_text}</div>
        <div class="orgapress-consent-banner-actions">
            <button type="button" class="orgapress-btn-secondary" id="orgapress-open-settings">${labels.settings}</button>
            <button type="button" class="orgapress-btn-primary" id="orgapress-banner-accept-all">${labels.accept_all}</button>
        </div>
    `;
    document.body.appendChild(banner);

    // Modal Creation
    const modal = document.createElement('div');
    modal.id = 'orgapress-consent-modal';
    modal.innerHTML = `
        <div class="orgapress-consent-backdrop"></div>
        <div class="orgapress-consent-box">
            <h2>${labels.privacy_settings}</h2>
            <p>${labels.modal_desc}</p>
            <form id="orgapress-consent-form" class="orgapress-consent-form"></form>
            <div class="orgapress-consent-actions">
                <button type="submit" form="orgapress-consent-form" class="orgapress-consent-save-selection">${labels.save_selection}</button>
                <button type="button" id="orgapress-consent-accept-all">${labels.accept_all}</button>
            </div>
        </div>
    `;
    document.body.appendChild(modal);

    // Trigger Icon Erstellen
    const trigger = document.createElement('div');
    trigger.id = 'orgapress-privacy-trigger';
    trigger.innerHTML = `<svg viewBox="0 0 24 24"><path d="M12,2C6.48,2,2,6.48,2,12s4.48,10,10,10s10-4.48,10-10S17.52,2,12,2z M12,20c-4.41,0-8-3.59-8-8s3.59-8,8-8s8,3.59,8,8 S16.41,20,12,20z M11,7h2v2h-2V7z M11,11h2v6h-2V11z"/></svg>`;
    document.body.appendChild(trigger);
    trigger.addEventListener('click', openModal);

    const form = modal.querySelector('#orgapress-consent-form');
    const categories = OrgaPressPrivacy.categories || {};
    const current = getConsent();

    Object.keys(categories).forEach(function (key) {
        const cat = categories[key];
        const checked = cat.required || current[key];
        const disabled = cat.required ? 'disabled' : '';
        const row = document.createElement('div');
        row.className = 'orgapress-consent-row';
        row.innerHTML = `
            <div class="orgapress-consent-label-wrap">
                <span class="orgapress-consent-label-title">${cat.label}</span>
            </div>
            <label class="orgapress-switch">
                <input type="checkbox" name="${key}" ${checked ? 'checked' : ''} ${disabled}>
                <span class="orgapress-slider"></span>
            </label>
        `;
        form.appendChild(row);
    });

    const acceptAllAction = function () {
        const state = {};
        Object.keys(categories).forEach(function (key) {
            state[key] = true;
        });
        setConsent(state);
        closeModal();
        hideBanner();
        if (window.OrgaPressPrivacyUnblock) {
            window.OrgaPressPrivacyUnblock();
        }
    };

    modal.querySelector('#orgapress-consent-accept-all').addEventListener('click', acceptAllAction);
    banner.querySelector('#orgapress-banner-accept-all').addEventListener('click', acceptAllAction);
    
    banner.querySelector('#orgapress-open-settings').addEventListener('click', openModal);

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const data = new FormData(form);
        const state = {};
        Object.keys(categories).forEach(function (key) {
            state[key] = categories[key].required || data.get(key) === 'on';
        });
        setConsent(state);
        closeModal();
        hideBanner();
        if (window.OrgaPressPrivacyUnblock) {
            window.OrgaPressPrivacyUnblock();
        }
    });
}

function openModal() {
    const modal = document.getElementById('orgapress-consent-modal');
    if (modal) modal.classList.add('is-visible');
}

function closeModal() {
    const modal = document.getElementById('orgapress-consent-modal');
    if (modal) modal.classList.remove('is-visible');
}

function hideBanner() {
    const banner = document.getElementById('orgapress-consent-banner');
    if (banner) banner.style.display = 'none';
}

document.addEventListener('DOMContentLoaded', function () {
    buildModal();
    if (document.cookie.includes(OrgaPressPrivacy.cookie + '=')) {
        hideBanner();
    }
});

})();
