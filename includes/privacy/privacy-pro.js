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

function unblock() {
    const consent = getConsent();
    document.querySelectorAll('script[type="text/plain"][data-category]').forEach(function (el) {
        const cat = el.getAttribute('data-category');
        if (consent[cat]) {
            const s = document.createElement('script');
            s.src = el.getAttribute('data-src');
            document.head.appendChild(s);
            el.parentNode.removeChild(el);
        }
    });
}

document.addEventListener('DOMContentLoaded', unblock);
window.OrgaPressPrivacyUnblock = unblock;

})();
