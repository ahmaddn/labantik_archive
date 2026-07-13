@php
    $defaultTop = $defaultTop ?? 8;
    $defaultBottom = $defaultBottom ?? 0;
    $defaultLeft = $defaultLeft ?? 15;
    $defaultRight = $defaultRight ?? 15;
    $docType = $docType ?? 'default';
@endphp

<div class="print-settings-wrapper" style="position: relative; display: inline-block;">
    <button type="button" class="btn btn-settings" id="btn-toggle-settings" style="background-color: #4f46e5; color: white;">
        <i class="fa-solid fa-sliders"></i> Atur Margin/Padding
    </button>

    <div class="settings-dropdown" id="settings-dropdown" style="display: none; position: absolute; right: 0; top: 100%; margin-top: 8px; width: 300px; background: white; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); border: 1px solid #e5e7eb; padding: 18px; z-index: 1010; color: #374151; font-family: system-ui, -apple-system, sans-serif; text-align: left; font-size: 14px;">
        <div style="font-weight: bold; margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f3f4f6; padding-bottom: 8px;">
            <span><i class="fa-solid fa-file-lines" style="color: #4f46e5;"></i> Pengaturan Halaman</span>
            <button type="button" id="btn-close-settings" style="border: none; background: none; font-size: 16px; cursor: pointer; color: #9ca3af; padding: 0 4px;"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <!-- Mode Selection -->
        <div style="margin-bottom: 15px;">
            <label style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 12px; color: #4b5563; text-transform: uppercase; letter-spacing: 0.5px;">Mode Margin</label>
            <div style="display: flex; gap: 8px; background-color: #f3f4f6; padding: 3px; border-radius: 6px;">
                <button type="button" id="mode-default" style="flex: 1; padding: 6px 10px; border: none; border-radius: 4px; font-size: 12px; font-weight: 500; cursor: pointer; transition: all 0.2s;" class="mode-btn">Default</button>
                <button type="button" id="mode-custom" style="flex: 1; padding: 6px 10px; border: none; border-radius: 4px; font-size: 12px; font-weight: 500; cursor: pointer; transition: all 0.2s;" class="mode-btn">Kustom</button>
            </div>
        </div>

        <!-- Custom Margin Controls -->
        <div id="custom-controls" style="display: none;">
            <div style="margin-bottom: 12px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                    <label for="pad-top" style="font-weight: 500; font-size: 13px;">Padding Atas (Top)</label>
                    <div style="display: flex; align-items: center; gap: 4px;">
                        <input type="number" id="num-top" min="0" max="60" value="{{ $defaultTop }}" style="width: 55px; padding: 2px 4px; border: 1px solid #d1d5db; border-radius: 4px; font-weight: bold; color: #4f46e5; font-size: 13px; text-align: center;">
                        <span style="font-size: 12px; color: #6b7280; font-weight: 500;">mm</span>
                    </div>
                </div>
                <input type="range" id="pad-top" min="0" max="60" value="{{ $defaultTop }}" style="width: 100%; accent-color: #4f46e5;">
            </div>

            <div style="margin-bottom: 12px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                    <label for="pad-bottom" style="font-weight: 500; font-size: 13px;">Padding Bawah (Bottom)</label>
                    <div style="display: flex; align-items: center; gap: 4px;">
                        <input type="number" id="num-bottom" min="0" max="60" value="{{ $defaultBottom }}" style="width: 55px; padding: 2px 4px; border: 1px solid #d1d5db; border-radius: 4px; font-weight: bold; color: #4f46e5; font-size: 13px; text-align: center;">
                        <span style="font-size: 12px; color: #6b7280; font-weight: 500;">mm</span>
                    </div>
                </div>
                <input type="range" id="pad-bottom" min="0" max="60" value="{{ $defaultBottom }}" style="width: 100%; accent-color: #4f46e5;">
            </div>

            <div style="margin-bottom: 12px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                    <label for="pad-left" style="font-weight: 500; font-size: 13px;">Padding Kiri (Left)</label>
                    <div style="display: flex; align-items: center; gap: 4px;">
                        <input type="number" id="num-left" min="0" max="60" value="{{ $defaultLeft }}" style="width: 55px; padding: 2px 4px; border: 1px solid #d1d5db; border-radius: 4px; font-weight: bold; color: #4f46e5; font-size: 13px; text-align: center;">
                        <span style="font-size: 12px; color: #6b7280; font-weight: 500;">mm</span>
                    </div>
                </div>
                <input type="range" id="pad-left" min="0" max="60" value="{{ $defaultLeft }}" style="width: 100%; accent-color: #4f46e5;">
            </div>

            <div style="margin-bottom: 15px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                    <label for="pad-right" style="font-weight: 500; font-size: 13px;">Padding Kanan (Right)</label>
                    <div style="display: flex; align-items: center; gap: 4px;">
                        <input type="number" id="num-right" min="0" max="60" value="{{ $defaultRight }}" style="width: 55px; padding: 2px 4px; border: 1px solid #d1d5db; border-radius: 4px; font-weight: bold; color: #4f46e5; font-size: 13px; text-align: center;">
                        <span style="font-size: 12px; color: #6b7280; font-weight: 500;">mm</span>
                    </div>
                </div>
                <input type="range" id="pad-right" min="0" max="60" value="{{ $defaultRight }}" style="width: 100%; accent-color: #4f46e5;">
            </div>
        </div>

        <div style="display: flex; gap: 8px; border-top: 1px solid #f3f4f6; padding-top: 12px; margin-top: 8px;">
            <button type="button" id="btn-reset-settings" style="flex: 1; padding: 8px 12px; border: 1px solid #d1d5db; background: white; color: #374151; font-size: 12px; font-weight: bold; border-radius: 5px; cursor: pointer; transition: all 0.2s;">
                <i class="fa-solid fa-arrow-rotate-left"></i> Reset
            </button>
            <button type="button" id="btn-save-settings" style="flex: 1; padding: 8px 12px; border: none; background: #10b981; color: white; font-size: 12px; font-weight: bold; border-radius: 5px; cursor: pointer; transition: all 0.2s;">
                <i class="fa-solid fa-check"></i> Selesai
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const wrapper = document.querySelector('.print-settings-wrapper');
    const toggleBtn = document.getElementById('btn-toggle-settings');
    const closeBtn = document.getElementById('btn-close-settings');
    const dropdown = document.getElementById('settings-dropdown');
    const modeDefault = document.getElementById('mode-default');
    const modeCustom = document.getElementById('mode-custom');
    const customControls = document.getElementById('custom-controls');
    const resetBtn = document.getElementById('btn-reset-settings');
    const saveBtn = document.getElementById('btn-save-settings');

    const ranges = {
        top: document.getElementById('pad-top'),
        bottom: document.getElementById('pad-bottom'),
        left: document.getElementById('pad-left'),
        right: document.getElementById('pad-right')
    };

    const numbers = {
        top: document.getElementById('num-top'),
        bottom: document.getElementById('num-bottom'),
        left: document.getElementById('num-left'),
        right: document.getElementById('num-right')
    };

    const defaults = {
        top: parseInt("{{ $defaultTop }}"),
        bottom: parseInt("{{ $defaultBottom }}"),
        left: parseInt("{{ $defaultLeft }}"),
        right: parseInt("{{ $defaultRight }}")
    };

    const docType = "{{ $docType }}";
    const storageKey = 'print_padding_' + docType;

    // Toggle Dropdown
    toggleBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        const isOpen = dropdown.style.display === 'block';
        dropdown.style.display = isOpen ? 'none' : 'block';
    });

    closeBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        dropdown.style.display = 'none';
    });

    saveBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        dropdown.style.display = 'none';
    });

    // Close on click outside
    document.addEventListener('click', function(event) {
        if (!wrapper.contains(event.target)) {
            dropdown.style.display = 'none';
        }
    });

    dropdown.addEventListener('click', function(e) {
        e.stopPropagation();
    });

    // Dynamic style element
    let styleEl = document.getElementById('dynamic-print-padding');
    if (!styleEl) {
        styleEl = document.createElement('style');
        styleEl.id = 'dynamic-print-padding';
        document.head.appendChild(styleEl);
    }

    function updateStyle(top, bottom, left, right, isCustom) {
        if (!isCustom) {
            styleEl.innerHTML = '';
            return;
        }

        styleEl.innerHTML = `
            .page, .page-pernyataan {
                padding-top: ${top}mm !important;
                padding-bottom: ${bottom}mm !important;
                padding-left: ${left}mm !important;
                padding-right: ${right}mm !important;
            }
            @media print {
                .page, .page-pernyataan {
                    padding: ${top}mm ${right}mm ${bottom}mm ${left}mm !important;
                }
            }
        `;
    }

    function setUIMode(customMode) {
        if (customMode) {
            modeCustom.style.backgroundColor = '#4f46e5';
            modeCustom.style.color = 'white';
            modeDefault.style.backgroundColor = 'transparent';
            modeDefault.style.color = '#4b5563';
            customControls.style.display = 'block';
        } else {
            modeDefault.style.backgroundColor = '#4f46e5';
            modeDefault.style.color = 'white';
            modeCustom.style.backgroundColor = 'transparent';
            modeCustom.style.color = '#4b5563';
            customControls.style.display = 'none';
        }
    }

    function saveToStorage(top, bottom, left, right, isCustom) {
        const data = { top, bottom, left, right, isCustom };
        localStorage.setItem(storageKey, JSON.stringify(data));
    }

    function loadFromStorage() {
        const stored = localStorage.getItem(storageKey);
        if (stored) {
            try {
                const data = JSON.parse(stored);
                return data;
            } catch(e) {
                return null;
            }
        }
        return null;
    }

    function applySettings() {
        const isCustom = modeCustom.style.backgroundColor !== 'transparent' && modeCustom.style.backgroundColor !== '';
        const top = parseInt(numbers.top.value) || 0;
        const bottom = parseInt(numbers.bottom.value) || 0;
        const left = parseInt(numbers.left.value) || 0;
        const right = parseInt(numbers.right.value) || 0;

        updateStyle(top, bottom, left, right, isCustom);
        saveToStorage(top, bottom, left, right, isCustom);
    }

    // Input listeners (range updates number, number updates range)
    Object.keys(ranges).forEach(key => {
        ranges[key].addEventListener('input', function() {
            numbers[key].value = this.value;
            applySettings();
        });
    });

    Object.keys(numbers).forEach(key => {
        numbers[key].addEventListener('input', function() {
            let val = parseInt(this.value) || 0;
            if (val < 0) val = 0;
            if (val > 100) val = 100; // soft cap
            this.value = val;
            ranges[key].value = val;
            applySettings();
        });
    });

    modeDefault.addEventListener('click', function() {
        setUIMode(false);
        applySettings();
    });

    modeCustom.addEventListener('click', function() {
        setUIMode(true);
        applySettings();
    });

    resetBtn.addEventListener('click', function() {
        Object.keys(ranges).forEach(key => {
            ranges[key].value = defaults[key];
            numbers[key].value = defaults[key];
        });
        setUIMode(false);
        applySettings();
        localStorage.removeItem(storageKey);
    });

    // Init
    const saved = loadFromStorage();
    if (saved) {
        Object.keys(ranges).forEach(key => {
            ranges[key].value = saved[key];
            numbers[key].value = saved[key];
        });
        setUIMode(saved.isCustom);
        updateStyle(saved.top, saved.bottom, saved.left, saved.right, saved.isCustom);
    } else {
        setUIMode(false);
        updateStyle(defaults.top, defaults.bottom, defaults.left, defaults.right, false);
    }
});
</script>
