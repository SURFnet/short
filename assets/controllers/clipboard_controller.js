import Clipboard from "stimulus-clipboard"

export default class extends Clipboard {
    connect() {
        super.connect()
    }

    // Function to override on copy.
    copy(event) {
        event.preventDefault();
        const el = document.createElement('textarea');
        el.value = this.sourceTarget.dataset.clipboardValue;
        el.setAttribute('readonly', '');
        el.style.position = 'absolute';
        el.style.left = '-9999px';
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);

        this.copied();
    }

    copied() {
        if (!this.hasButtonTarget) return;

        if (this.timeout) {
            clearTimeout(this.timeout);
        }

        const originalText = this.sourceTarget.getAttribute('aria-label');
        this.sourceTarget.setAttribute('aria-label', this.data.get('successContent'));
        this.timeout = setTimeout(() => {
            this.sourceTarget.setAttribute('aria-label', originalText);
        }, this.successDuration);
    }
}
