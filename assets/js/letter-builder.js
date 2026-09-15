/**
 * SIMANTAP - Letter Builder Studio Controller & Auto-Save Engine
 * Loaded as external script to comply with CSP (script-src 'self')
 */
(function() {
    'use strict';

    window.currentBuilderTabIdx = 0;
    window.currentA4Zoom = 100;
    var autoSaveTimer = null;
    var DRAFT_STORAGE_KEY = (function() {
        var urlParams = new URLSearchParams(window.location.search);
        var page = urlParams.get('page') || 'create-document';
        var id = urlParams.get('id') || '';
        return 'simantab_draft_' + page + (id ? ('_' + id) : '');
    })();

    // 1. Tab Switching Function
    window.switchBuilderTab = function(idx) {
        idx = parseInt(idx, 10);
        if (isNaN(idx) || idx < 0) idx = 0;
        if (idx > 3) idx = 3;
        window.currentBuilderTabIdx = idx;

        var paneIds = ['tab-meta', 'tab-kop', 'tab-content', 'tab-sign'];

        // Toggle buttons active state
        var btns = document.querySelectorAll('.builder-tab-btn');
        for (var i = 0; i < btns.length; i++) {
            if (i === idx) {
                btns[i].classList.add('active');
            } else {
                btns[i].classList.remove('active');
            }
        }

        // Toggle tab panes display
        for (var j = 0; j < paneIds.length; j++) {
            var pane = document.getElementById(paneIds[j]);
            if (pane) {
                if (j === idx) {
                    pane.classList.add('active');
                    pane.style.setProperty('display', 'block', 'important');
                } else {
                    pane.classList.remove('active');
                    pane.style.setProperty('display', 'none', 'important');
                }
            }
        }

        // Update Wizard navigation buttons
        var prevBtn = document.getElementById('prevTabBtn');
        var nextBtn = document.getElementById('nextTabBtn');
        if (prevBtn) {
            prevBtn.style.display = (idx > 0 ? 'inline-flex' : 'none');
        }
        if (nextBtn) {
            if (idx >= 3) {
                nextBtn.style.display = 'none';
            } else {
                nextBtn.style.display = 'inline-flex';
                var labels = ['KOP, Logo & Garis', 'Tujuan & Isi Surat', 'Tanda Tangan & Cap'];
                nextBtn.innerHTML = 'Lanjut ke ' + labels[idx] + ' <i class="ri-arrow-right-line"></i>';
            }
        }

        try {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } catch (e) {}

        window.triggerAutoSave();
    };

    // 2. View Mode Toggle (Split / Form Only / Preview Only)
    window.setWorkViewMode = function(mode) {
        var wb = document.getElementById('builderWorkbench');
        var btns = document.querySelectorAll('[data-view-mode]');
        for (var i = 0; i < btns.length; i++) {
            if (btns[i].getAttribute('data-view-mode') === mode) {
                btns[i].classList.add('active');
            } else {
                btns[i].classList.remove('active');
            }
        }
        if (wb) {
            wb.classList.remove('view-form-only', 'view-preview-only');
            if (mode === 'form') wb.classList.add('view-form-only');
            if (mode === 'preview') wb.classList.add('view-preview-only');
        }
    };

    // 3. Zoom Preview Engine
    window.applyA4Zoom = function(delta) {
        window.currentA4Zoom = Math.min(140, Math.max(60, (window.currentA4Zoom || 100) + delta));
        var sheet = document.getElementById('a4Sheet');
        var lbl = document.getElementById('zoomLabel');
        if (sheet) sheet.style.transform = 'scale(' + (window.currentA4Zoom / 100) + ')';
        if (lbl) lbl.textContent = window.currentA4Zoom + '%';
    };

    window.resetA4Zoom = function() {
        window.currentA4Zoom = 100;
        var sheet = document.getElementById('a4Sheet');
        var lbl = document.getElementById('zoomLabel');
        if (sheet) sheet.style.transform = 'scale(1)';
        if (lbl) lbl.textContent = '100%';
    };

    // 4. Auto vs Manual Numbering Toggle
    window.toggleNumberMode = function(val) {
        var isManual = (val === 'manual');
        var manualWrap = document.getElementById('manualNumberWrap');
        var schemeWrap = document.getElementById('schemeWrap');
        if (manualWrap) manualWrap.style.display = isManual ? 'flex' : 'none';
        if (schemeWrap) schemeWrap.style.display = isManual ? 'none' : 'flex';
        window.updateA4();
    };

    // 5. Greeting Quick Presets
    window.setOpeningGreeting = function(text) {
        var el = document.getElementById('greeting_opening');
        if (el) { el.value = text; window.updateA4(); window.triggerAutoSave(); }
    };

    window.setClosingGreeting = function(text) {
        var el = document.getElementById('greeting_closing');
        if (el) { el.value = text; window.updateA4(); window.triggerAutoSave(); }
    };

    // 6. Live Real-Time A4 Sheet Updater (Granular Size & Style Rendering)
    window.updateA4 = function() {
        try {
            var a4Sheet = document.getElementById('a4Sheet');

            // Paper Margin / Padding
            var paperMarginEl = document.getElementById('paper_margin');
            var paperMargin = paperMarginEl ? paperMarginEl.value : 'normal';
            if (a4Sheet) {
                switch(paperMargin) {
                    case 'compact':
                        a4Sheet.style.padding = '24px 30px';
                        break;
                    case 'spacious':
                        a4Sheet.style.padding = '52px 58px';
                        break;
                    default:
                        a4Sheet.style.padding = '42px 48px';
                }
            }

            // KOP Texts & Font Sizes
            var inst1 = document.getElementById('kop_inst1');
            var inst2 = document.getElementById('kop_inst2');
            var addr = document.getElementById('kop_address');
            var contact = document.getElementById('kop_contact');

            var a4Inst1 = document.getElementById('a4KopInst1');
            var a4Inst2 = document.getElementById('a4KopInst2');
            var a4Addr = document.getElementById('a4KopAddress');
            var a4Contact = document.getElementById('a4KopContact');

            var inst1SizeEl = document.getElementById('kop_inst1_size');
            var inst2SizeEl = document.getElementById('kop_inst2_size');
            var addrSizeEl = document.getElementById('kop_address_size');
            var contactSizeEl = document.getElementById('kop_contact_size');

            if (inst1 && a4Inst1) {
                a4Inst1.textContent = inst1.value || 'UNIVERSITAS MUHAMMADIYAH PONOROGO';
                if (inst1SizeEl) a4Inst1.style.fontSize = inst1SizeEl.value;
            }
            if (inst2 && a4Inst2) {
                a4Inst2.textContent = inst2.value || '';
                if (inst2SizeEl) a4Inst2.style.fontSize = inst2SizeEl.value;
                a4Inst2.style.display = inst2.value.trim() ? 'block' : 'none';
            }
            if (addr && a4Addr) {
                a4Addr.textContent = addr.value || '';
                if (addrSizeEl) a4Addr.style.fontSize = addrSizeEl.value;
            }
            if (contact && a4Contact) {
                a4Contact.textContent = contact.value || '';
                if (contactSizeEl) a4Contact.style.fontSize = contactSizeEl.value;
                a4Contact.style.display = contact.value.trim() ? 'block' : 'none';
            }

            // KOP Alignment
            var kopAlignEl = document.getElementById('kop_align');
            var kopAlign = kopAlignEl ? kopAlignEl.value : 'center';
            var kopCell = document.getElementById('a4KopTextCell');
            if (kopCell) kopCell.style.textAlign = kopAlign;

            // Font Family
            var fontEl = document.getElementById('font_family');
            var fontFamily = fontEl ? fontEl.value : 'serif';
            if (a4Sheet) {
                a4Sheet.style.fontFamily = fontFamily === 'sans' ?
                    '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif' :
                    '"Times New Roman", Times, Georgia, serif';
            }

            // Divider Line, Spacing & Weight
            var lineRadio = document.querySelector('input[name="kop_line_style"]:checked');
            var lineStyle = lineRadio ? lineRadio.value : 'double';
            var kopSpacingEl = document.getElementById('kop_spacing');
            var kopSpacing = kopSpacingEl ? kopSpacingEl.value : 'normal';
            var kopWeightEl = document.getElementById('kop_line_weight');
            var kopWeight = kopWeightEl ? kopWeightEl.value : 'normal';

            var lineMargin = '6px 0 16px';
            if (kopSpacing === 'compact') lineMargin = '2px 0 10px';
            if (kopSpacing === 'spacious') lineMargin = '10px 0 22px';

            var lineThickness = '3px';
            if (kopWeight === 'thin') lineThickness = '1.5px';
            if (kopWeight === 'thick') lineThickness = '4.5px';

            var lineEl = document.getElementById('a4KopLine');
            if (lineEl) {
                lineEl.className = '';
                lineEl.style.margin = lineMargin;
                switch(lineStyle) {
                    case 'single_thick':
                        lineEl.className = 'a4-line-single-thick';
                        lineEl.style.borderTop = lineThickness + ' solid #000';
                        break;
                    case 'single_thin':
                        lineEl.className = 'a4-line-single-thin';
                        lineEl.style.borderTop = '1px solid #000';
                        break;
                    case 'dashed':
                        lineEl.className = 'a4-line-dashed';
                        lineEl.style.borderTop = '1.5px dashed #000';
                        break;
                    case 'none':
                        lineEl.className = 'a4-line-none';
                        lineEl.style.border = 'none';
                        break;
                    default:
                        lineEl.className = 'a4-line-double';
                        lineEl.style.borderTop = lineThickness + ' solid #000';
                        lineEl.style.borderBottom = '1px solid #000';
                }
            }

            // Document Number & Heading Size
            var manualRadio = document.querySelector('input[name="number_mode"][value="manual"]');
            var isManual = manualRadio ? manualRadio.checked : false;
            var customNum = document.getElementById('custom_number');
            var a4Number = document.getElementById('a4Number');
            if (a4Number) {
                a4Number.textContent = isManual ?
                    ((customNum && customNum.value.trim()) || '[Ketikkan Nomor Surat]') :
                    '[DRAF — Ditentukan Sistem]';
            }

            // Meta Info (Nomor, Sifat, Lampiran, Perihal, Tanggal)
            var metaSizeEl = document.getElementById('meta_font_size');
            var metaSize = metaSizeEl ? metaSizeEl.value : '11pt';
            var metaTable = document.querySelector('.a4-meta-table');
            if (metaTable) metaTable.style.fontSize = metaSize;

            var conf = document.getElementById('confidentiality');
            var a4Conf = document.getElementById('a4Confidentiality');
            if (conf && a4Conf) a4Conf.textContent = conf.value;

            var att = document.getElementById('attachment');
            var a4Att = document.getElementById('a4Attachment');
            if (att && a4Att) a4Att.textContent = att.value || '-';

            var tit = document.getElementById('title');
            var a4Tit = document.getElementById('a4Title');
            if (tit && a4Tit) a4Tit.textContent = tit.value || '[Perihal Surat]';

            // City, Date & Date Size
            var cityEl = document.getElementById('city');
            var city = cityEl ? cityEl.value.trim() : '';
            var docDateEl = document.getElementById('document_date');
            var docDate = docDateEl ? docDateEl.value : '';
            var dateSizeEl = document.getElementById('date_size');
            var dateSize = dateSizeEl ? dateSizeEl.value : '11pt';

            var formattedDate = docDate;
            if (docDate) {
                var d = new Date(docDate);
                if (!isNaN(d.getTime())) {
                    var months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                    formattedDate = d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
                }
            }
            var a4DateCity = document.getElementById('a4DateCity');
            if (a4DateCity) {
                a4DateCity.textContent = city ? (city + ', ' + formattedDate) : formattedDate;
                a4DateCity.style.fontSize = dateSize;
            }

            // Recipient & Recipient Size
            var rec = document.getElementById('recipient');
            var a4Rec = document.getElementById('a4Recipient');
            var recSizeEl = document.getElementById('recipient_size');
            if (rec && a4Rec) {
                a4Rec.textContent = rec.value || '-';
                if (recSizeEl) a4Rec.style.fontSize = recSizeEl.value;
            }

            // Opening & Size
            var gOpen = document.getElementById('greeting_opening');
            var a4GOpen = document.getElementById('a4GreetingOpening');
            var gOpenSizeEl = document.getElementById('greeting_opening_size');
            if (gOpen && a4GOpen) {
                a4GOpen.textContent = gOpen.value;
                if (gOpenSizeEl) a4GOpen.style.fontSize = gOpenSizeEl.value;
            }

            var openEl = document.getElementById('opening');
            var a4Open = document.getElementById('a4Opening');
            var openSizeEl = document.getElementById('opening_size');
            if (openEl && a4Open) {
                a4Open.textContent = openEl.value;
                if (openSizeEl) a4Open.style.fontSize = openSizeEl.value;
            }

            // Body, Line Height & Spacing
            var bodyEl = document.getElementById('body');
            var a4Body = document.getElementById('a4Body');
            var bodySizeEl = document.getElementById('body_size');
            var bodyLineHeightEl = document.getElementById('body_line_height');
            var pSpacingEl = document.getElementById('paragraph_spacing');

            var bodyLineHeight = bodyLineHeightEl ? bodyLineHeightEl.value : '1.6';
            var pMargin = '10px';
            if (pSpacingEl) {
                if (pSpacingEl.value === 'compact') pMargin = '6px';
                if (pSpacingEl.value === 'relaxed') pMargin = '16px';
            }

            if (bodyEl && a4Body) {
                a4Body.textContent = bodyEl.value;
                if (bodySizeEl) a4Body.style.fontSize = bodySizeEl.value;
                a4Body.style.lineHeight = bodyLineHeight;
                a4Body.style.marginBottom = pMargin;
            }
            if (a4Open) {
                a4Open.style.lineHeight = bodyLineHeight;
                a4Open.style.marginBottom = pMargin;
            }

            // Closing & Size
            var closeEl = document.getElementById('closing');
            var a4Close = document.getElementById('a4Closing');
            var closeSizeEl = document.getElementById('closing_size');
            if (closeEl && a4Close) {
                a4Close.textContent = closeEl.value;
                if (closeSizeEl) a4Close.style.fontSize = closeSizeEl.value;
                a4Close.style.lineHeight = bodyLineHeight;
                a4Close.style.marginBottom = pMargin;
            }

            var gClose = document.getElementById('greeting_closing');
            var a4GClose = document.getElementById('a4GreetingClosing');
            var gCloseSizeEl = document.getElementById('greeting_closing_size');
            if (gClose && a4GClose) {
                a4GClose.textContent = gClose.value;
                if (gCloseSizeEl) a4GClose.style.fontSize = gCloseSizeEl.value;
            }

            // Signer Info & Sizes
            var signPos = document.getElementById('signer_position');
            var a4SignPos = document.getElementById('a4SignPosition');
            var signPosSizeEl = document.getElementById('signer_position_size');
            if (signPos && a4SignPos) {
                a4SignPos.textContent = signPos.value || 'Pejabat Penandatangan';
                if (signPosSizeEl) a4SignPos.style.fontSize = signPosSizeEl.value;
            }

            var signName = document.getElementById('signer_name');
            var a4SignName = document.getElementById('a4SignName');
            var signNameSizeEl = document.getElementById('signer_name_size');
            if (signName && a4SignName) {
                a4SignName.textContent = signName.value || 'Nama Pejabat';
                if (signNameSizeEl) a4SignName.style.fontSize = signNameSizeEl.value;
            }

            var signNip = document.getElementById('signer_number');
            var a4SignNip = document.getElementById('a4SignNip');
            var signNipSizeEl = document.getElementById('signer_number_size');
            if (signNip && a4SignNip) {
                a4SignNip.textContent = signNip.value || '';
                if (signNipSizeEl) a4SignNip.style.fontSize = signNipSizeEl.value;
                a4SignNip.style.display = signNip.value.trim() ? 'block' : 'none';
            }

            // Signature & Stamp Dimension Controls
            var signSpaceEl = document.getElementById('signature_space_height');
            var signSpace = signSpaceEl ? signSpaceEl.value : '80px';
            var a4SignMiddle = document.getElementById('a4SignMiddle');
            if (a4SignMiddle) a4SignMiddle.style.height = signSpace;

            var signSizeEl = document.getElementById('signature_size');
            var signSize = signSizeEl ? signSizeEl.value : 'medium';
            var signMaxH = signSize === 'small' ? '50px' : (signSize === 'large' ? '90px' : (signSize === 'xlarge' ? '110px' : '70px'));
            var a4SignImg = document.getElementById('a4SignImg');
            if (a4SignImg) a4SignImg.style.maxHeight = signMaxH;

            var stampSizeEl = document.getElementById('stamp_size');
            var stampSize = stampSizeEl ? stampSizeEl.value : 'medium';
            var stampDim = stampSize === 'small' ? '65px' : (stampSize === 'large' ? '105px' : (stampSize === 'xlarge' ? '125px' : '85px'));

            var stampPosEl = document.getElementById('stamp_position');
            var stampPos = stampPosEl ? stampPosEl.value : 'left';
            var stampOpacityEl = document.getElementById('stamp_opacity');
            var stampOp = stampOpacityEl ? parseInt(stampOpacityEl.value || '85', 10) : 85;

            var a4Stamp = document.getElementById('a4StampImg');
            if (a4Stamp) {
                a4Stamp.className = 'a4-stamp-img a4-stamp-' + stampPos;
                a4Stamp.style.maxWidth = stampDim;
                a4Stamp.style.maxHeight = stampDim;
                a4Stamp.style.opacity = (stampOp / 100);
            }

            // Copies & Footer Note Font Sizes
            var copiesEl = document.getElementById('copies');
            var copies = copiesEl ? copiesEl.value.trim() : '';
            var copiesWrap = document.getElementById('a4CopiesWrap');
            var a4Copies = document.getElementById('a4Copies');
            var copiesSizeEl = document.getElementById('copies_size');
            if (copiesWrap && a4Copies) {
                copiesWrap.style.display = copies ? 'block' : 'none';
                a4Copies.textContent = copies;
                if (copiesSizeEl) a4Copies.style.fontSize = copiesSizeEl.value;
            }

            var footerEl = document.getElementById('footer_note');
            var footer = footerEl ? footerEl.value.trim() : '';
            var footerWrap = document.getElementById('a4FooterNoteWrap');
            var a4Footer = document.getElementById('a4FooterNote');
            var footerSizeEl = document.getElementById('footer_size');
            if (footerWrap && a4Footer) {
                footerWrap.style.display = footer ? 'flex' : 'none';
                a4Footer.textContent = footer;
                if (footerSizeEl) a4Footer.style.fontSize = footerSizeEl.value;
            }
        } catch (err) {
            console.warn('LetterBuilder update error:', err);
        }
    };

    // 7. Auto-Save Engine (Client-Side LocalStorage with Zero Server Load)
    window.triggerAutoSave = function() {
        var statusBadge = document.getElementById('autoSaveStatus');
        var statusText = document.getElementById('autoSaveText');
        if (statusBadge && statusText) {
            statusBadge.className = 'autosave-badge saving';
            statusText.textContent = 'Menyimpan draf...';
        }

        if (autoSaveTimer) clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(function() {
            window.performAutoSave();
        }, 280);
    };

    window.performAutoSave = function() {
        try {
            var form = document.getElementById('letterBuilderForm');
            if (!form) return;

            var draftData = {
                timestamp: Date.now(),
                tabIndex: window.currentBuilderTabIdx || 0,
                fields: {}
            };

            var inputs = form.querySelectorAll('input:not([type="file"]), select, textarea');
            for (var i = 0; i < inputs.length; i++) {
                var el = inputs[i];
                if (!el.name) continue;
                if (el.type === 'radio') {
                    if (el.checked) draftData.fields[el.name] = el.value;
                } else if (el.type === 'checkbox') {
                    draftData.fields[el.name] = el.checked;
                } else {
                    draftData.fields[el.name] = el.value;
                }
            }

            localStorage.setItem(DRAFT_STORAGE_KEY, JSON.stringify(draftData));

            var statusBadge = document.getElementById('autoSaveStatus');
            var statusText = document.getElementById('autoSaveText');
            var statusTime = document.getElementById('autoSaveTime');
            if (statusBadge && statusText) {
                statusBadge.className = 'autosave-badge saved';
                statusText.textContent = 'Draf tersimpan otomatis';
                var now = new Date();
                var timeStr = ('0' + now.getHours()).slice(-2) + ':' + ('0' + now.getMinutes()).slice(-2) + ':' + ('0' + now.getSeconds()).slice(-2);
                if (statusTime) statusTime.textContent = '(' + timeStr + ')';
            }
        } catch (err) {
            console.warn('AutoSave storage error:', err);
        }
    };

    window.checkExistingDraft = function() {
        try {
            var saved = localStorage.getItem(DRAFT_STORAGE_KEY);
            if (!saved) return;
            var data = JSON.parse(saved);
            if (!data || !data.fields) return;

            // Show Recovery Banner
            var banner = document.getElementById('draftRecoveryAlert');
            var msg = document.getElementById('draftRecoveryMsg');
            var btnRestore = document.getElementById('btnRestoreDraft');
            var btnDiscard = document.getElementById('btnDiscardDraft');

            if (banner && msg) {
                var d = new Date(data.timestamp || Date.now());
                var timeStr = d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                msg.textContent = 'Ditemukan draf surat yang tersimpan otomatis pada ' + timeStr + '. Ingin memulihkan pekerjaan Anda?';
                banner.style.display = 'flex';

                if (btnRestore) {
                    btnRestore.onclick = function() {
                        window.restoreDraftData(data);
                        banner.style.display = 'none';
                    };
                }
                if (btnDiscard) {
                    btnDiscard.onclick = function() {
                        localStorage.removeItem(DRAFT_STORAGE_KEY);
                        banner.style.display = 'none';
                    };
                }
            }
        } catch (e) {
            console.warn('Check draft error:', e);
        }
    };

    window.restoreDraftData = function(data) {
        if (!data || !data.fields) return;
        var form = document.getElementById('letterBuilderForm');
        if (!form) return;

        for (var name in data.fields) {
            if (!Object.prototype.hasOwnProperty.call(data.fields, name)) continue;
            var val = data.fields[name];
            var radios = form.querySelectorAll('input[type="radio"][name="' + name + '"]');
            if (radios.length > 0) {
                for (var r = 0; r < radios.length; r++) {
                    radios[r].checked = (radios[r].value === val);
                    var pill = radios[r].closest('.pill-opt');
                    if (pill) pill.classList.toggle('selected', radios[r].checked);
                }
            } else {
                var input = form.querySelector('[name="' + name + '"]');
                if (input) {
                    input.value = val;
                }
            }
        }

        // Restore image base64 previews
        var logoB64 = document.getElementById('logo_base64');
        if (logoB64 && logoB64.value) {
            var lp = document.getElementById('logoPreviewBox');
            var la = document.getElementById('a4LogoLeftImg');
            var lr = document.getElementById('removeLogoBtn');
            if (lp) lp.innerHTML = '<img src="' + logoB64.value + '" alt="Logo">';
            if (la) { la.src = logoB64.value; la.style.display = 'inline-block'; }
            if (lr) lr.style.display = 'inline-flex';
        }

        var signB64 = document.getElementById('signature_base64');
        if (signB64 && signB64.value) {
            var sp = document.getElementById('signPreviewBox');
            var sa = document.getElementById('a4SignImg');
            var sr = document.getElementById('removeSignBtn');
            if (sp) sp.innerHTML = '<img src="' + signB64.value + '" alt="TTD">';
            if (sa) { sa.src = signB64.value; sa.style.display = 'inline-block'; }
            if (sr) sr.style.display = 'inline-flex';
        }

        var stampB64 = document.getElementById('stamp_base64');
        if (stampB64 && stampB64.value) {
            var stp = document.getElementById('stampPreviewBox');
            var sta = document.getElementById('a4StampImg');
            var str = document.getElementById('removeStampBtn');
            if (stp) stp.innerHTML = '<img src="' + stampB64.value + '" alt="Cap">';
            if (sta) { sta.src = stampB64.value; sta.style.display = 'inline-block'; }
            if (str) str.style.display = 'inline-flex';
        }

        if (typeof data.tabIndex === 'number') {
            window.switchBuilderTab(data.tabIndex);
        }
        window.updateA4();
    };

    // Helper for file uploads
    function bindFileUpload(fileInputId, base64InputId, previewBoxId, a4ImgId, removeBtnId, onComplete) {
        var fileInput = document.getElementById(fileInputId);
        var base64Input = document.getElementById(base64InputId);
        var previewBox = document.getElementById(previewBoxId);
        var a4Img = document.getElementById(a4ImgId);
        var removeBtn = document.getElementById(removeBtnId);

        if (!fileInput) return;

        fileInput.addEventListener('change', function() {
            var file = fileInput.files[0];
            if (!file) return;
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file maksimal 2 MB.');
                fileInput.value = '';
                return;
            }
            var reader = new FileReader();
            reader.onload = function(e) {
                var b64 = e.target.result;
                if (base64Input) base64Input.value = b64;
                if (previewBox) previewBox.innerHTML = '<img src="' + b64 + '" alt="Pratinjau">';
                if (a4Img) {
                    a4Img.src = b64;
                    a4Img.style.display = 'inline-block';
                }
                if (removeBtn) removeBtn.style.display = 'inline-flex';
                if (onComplete) onComplete(b64);
                window.updateA4();
                window.triggerAutoSave();
            };
            reader.readAsDataURL(file);
        });

        if (removeBtn) {
            removeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                fileInput.value = '';
                if (base64Input) base64Input.value = '';
                if (previewBox) previewBox.innerHTML = '<span class="placeholder-icon"><i class="ri-image-add-line"></i></span>';
                if (a4Img) {
                    a4Img.src = '';
                    a4Img.style.display = 'none';
                }
                removeBtn.style.display = 'none';
                if (onComplete) onComplete('');
                window.updateA4();
                window.triggerAutoSave();
            });
        }
    }

    // 8. Initialize Letter Builder on DOM Ready
    function initLetterBuilder() {
        // Tab buttons event delegation
        document.addEventListener('click', function(e) {
            var btn = e.target.closest('.builder-tab-btn');
            if (btn) {
                e.preventDefault();
                var allBtns = Array.prototype.slice.call(document.querySelectorAll('.builder-tab-btn'));
                var idx = allBtns.indexOf(btn);
                if (idx !== -1) window.switchBuilderTab(idx);
            }
        });

        // Wizard navigation buttons
        var prevBtn = document.getElementById('prevTabBtn');
        if (prevBtn) {
            prevBtn.addEventListener('click', function() {
                window.switchBuilderTab((window.currentBuilderTabIdx || 0) - 1);
            });
        }
        var nextBtn = document.getElementById('nextTabBtn');
        if (nextBtn) {
            nextBtn.addEventListener('click', function() {
                window.switchBuilderTab((window.currentBuilderTabIdx || 0) + 1);
            });
        }

        // View Mode Toggles
        var viewBtns = document.querySelectorAll('[data-view-mode]');
        for (var i = 0; i < viewBtns.length; i++) {
            viewBtns[i].addEventListener('click', function(e) {
                e.preventDefault();
                window.setWorkViewMode(this.getAttribute('data-view-mode'));
            });
        }

        // Zoom Buttons
        var zoomIn = document.getElementById('zoomInBtn');
        if (zoomIn) zoomIn.addEventListener('click', function() { window.applyA4Zoom(10); });
        var zoomOut = document.getElementById('zoomOutBtn');
        if (zoomOut) zoomOut.addEventListener('click', function() { window.applyA4Zoom(-10); });
        var zoomReset = document.getElementById('zoomResetBtn');
        if (zoomReset) zoomReset.addEventListener('click', function() { window.resetA4Zoom(); });

        // Number mode radio
        var numRadios = document.querySelectorAll('input[name="number_mode"]');
        for (var r = 0; r < numRadios.length; r++) {
            numRadios[r].addEventListener('change', function() {
                var allModeRadios = document.querySelectorAll('input[name="number_mode"]');
                for (var m = 0; m < allModeRadios.length; m++) {
                    var pill = allModeRadios[m].closest('.pill-opt');
                    if (pill) pill.classList.toggle('selected', allModeRadios[m].checked);
                }
                window.toggleNumberMode(this.value);
                window.triggerAutoSave();
            });
        }

        // Pill radio sync for all radios
        var allPills = document.querySelectorAll('.pill-opt input[type="radio"]');
        for (var p = 0; p < allPills.length; p++) {
            allPills[p].addEventListener('change', function() {
                var groupName = this.name;
                var sameGroup = document.querySelectorAll('input[name="' + groupName + '"]');
                for (var g = 0; g < sameGroup.length; g++) {
                    var opt = sameGroup[g].closest('.pill-opt');
                    if (opt) opt.classList.toggle('selected', sameGroup[g].checked);
                }
                window.updateA4();
                window.triggerAutoSave();
            });
        }

        // Signer Select auto fill
        var signerSelect = document.getElementById('signer_employee_id');
        if (signerSelect) {
            signerSelect.addEventListener('change', function() {
                var opt = this.selectedOptions ? this.selectedOptions[0] : null;
                if (opt && opt.value) {
                    var sn = document.getElementById('signer_name');
                    var sp = document.getElementById('signer_position');
                    var snum = document.getElementById('signer_number');
                    if (sn) sn.value = opt.getAttribute('data-name') || '';
                    if (sp) sp.value = opt.getAttribute('data-pos') || 'Pejabat Penandatangan';
                    var nip = opt.getAttribute('data-nip');
                    if (snum) snum.value = nip ? ('NIP. ' + nip) : '';
                    window.updateA4();
                    window.triggerAutoSave();
                }
            });
        }

        // Logo size listener
        var logoSize = document.getElementById('logo_size');
        if (logoSize) {
            logoSize.addEventListener('change', function() {
                var sz = this.value === 'small' ? '56px' : (this.value === 'large' ? '88px' : (this.value === 'xlarge' ? '104px' : '72px'));
                var l1 = document.getElementById('a4LogoLeftImg');
                var l2 = document.getElementById('a4LogoRightImg');
                if (l1) { l1.style.maxHeight = sz; l1.style.maxWidth = sz; }
                if (l2) { l2.style.maxHeight = sz; l2.style.maxWidth = sz; }
            });
        }

        // Logo align listener
        var logoAlign = document.getElementById('logo_align');
        if (logoAlign) {
            logoAlign.addEventListener('change', function() {
                var align = this.value;
                var leftCell = document.getElementById('a4LogoLeftCell');
                var rightCell = document.getElementById('a4LogoRightCell');
                var logoB64 = document.getElementById('logo_base64');
                var hasLogo = logoB64 ? !!logoB64.value : false;
                if (leftCell) leftCell.style.display = (align === 'none' || !hasLogo) ? 'none' : 'table-cell';
                if (rightCell) rightCell.style.display = (align === 'both' && hasLogo) ? 'table-cell' : 'none';
            });
        }

        // Attach inputs live update & auto-save trigger
        var formInputs = document.querySelectorAll('#letterBuilderForm input:not([type="file"]), #letterBuilderForm select, #letterBuilderForm textarea');
        for (var fi = 0; fi < formInputs.length; fi++) {
            formInputs[fi].addEventListener('input', function() {
                window.updateA4();
                window.triggerAutoSave();
            });
            formInputs[fi].addEventListener('change', function() {
                window.updateA4();
                window.triggerAutoSave();
            });
        }

        // Clear local draft on form submit
        var builderForm = document.getElementById('letterBuilderForm');
        if (builderForm) {
            builderForm.addEventListener('submit', function() {
                try {
                    localStorage.removeItem(DRAFT_STORAGE_KEY);
                } catch (e) {}
            });
        }

        // Helper to restore existing base64 value
        function restoreExistingUpload(base64InputId, previewBoxId, a4ImgId, removeBtnId, onComplete) {
            var base64Input = document.getElementById(base64InputId);
            var previewBox = document.getElementById(previewBoxId);
            var a4Img = document.getElementById(a4ImgId);
            var removeBtn = document.getElementById(removeBtnId);
            if (base64Input && base64Input.value && base64Input.value.trim() !== '') {
                var b64 = base64Input.value.trim();
                if (previewBox) previewBox.innerHTML = '<img src="' + b64 + '" alt="Pratinjau">';
                if (a4Img) {
                    a4Img.src = b64;
                    a4Img.style.display = 'inline-block';
                }
                if (removeBtn) removeBtn.style.display = 'inline-flex';
                if (onComplete) onComplete(b64);
            }
        }

        // Upload Handlers
        var updateLogoOnA4 = function(base64) {
            var leftCell = document.getElementById('a4LogoLeftCell');
            var alignEl = document.getElementById('logo_align');
            var align = alignEl ? alignEl.value : 'left';
            if (leftCell) leftCell.style.display = (base64 && align !== 'none') ? 'table-cell' : 'none';
            if (align === 'both') {
                var rImg = document.getElementById('a4LogoRightImg');
                var rCell = document.getElementById('a4LogoRightCell');
                if (rImg) rImg.src = base64;
                if (rCell) rCell.style.display = base64 ? 'table-cell' : 'none';
            }
        };

        bindFileUpload('logoFileInput', 'logo_base64', 'logoPreviewBox', 'a4LogoLeftImg', 'removeLogoBtn', updateLogoOnA4);
        bindFileUpload('signFileInput', 'signature_base64', 'signPreviewBox', 'a4SignImg', 'removeSignBtn');
        bindFileUpload('stampFileInput', 'stamp_base64', 'stampPreviewBox', 'a4StampImg', 'removeStampBtn');

        // Restore if already filled
        restoreExistingUpload('logo_base64', 'logoPreviewBox', 'a4LogoLeftImg', 'removeLogoBtn', updateLogoOnA4);
        restoreExistingUpload('signature_base64', 'signPreviewBox', 'a4SignImg', 'removeSignBtn');
        restoreExistingUpload('stamp_base64', 'stampPreviewBox', 'a4StampImg', 'removeStampBtn');

        // Initial setup
        window.switchBuilderTab(0);
        window.updateA4();
        window.checkExistingDraft();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initLetterBuilder);
    } else {
        initLetterBuilder();
    }
})();
