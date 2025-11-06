(function initializeRoomUI(window) {
    const context = window.RoomContext;
    if (!context) {
        throw new Error('RoomContext is required before initializing RoomUI');
    }

    const { state, dom, toastInstance } = context;

    function scrollToBottom() {
        if (!dom.chatFeed) return;
        dom.chatFeed.scrollTop = dom.chatFeed.scrollHeight;
    }

    function formatTimestamp(value) {
        if (!value) return '';
        const date = new Date(value);
        return `${date.toLocaleDateString()} ${date.toLocaleTimeString()}`;
    }

    function appendSystemMessage(text) {
        if (!dom.chatFeed) return;
        const wrapper = document.createElement('div');
        wrapper.className = 'text-center text-secondary small my-2';
        wrapper.textContent = text;
        dom.chatFeed.appendChild(wrapper);
        scrollToBottom();
    }

    function fileTypeIcon(mime) {
        if (!mime) return 'fa-file';
        if (mime.startsWith('image/')) return 'fa-file-image';
        if (mime.startsWith('video/')) return 'fa-file-video';
        if (mime === 'application/pdf') return 'fa-file-pdf';
        if (mime.startsWith('audio/')) return 'fa-file-audio';
        if (mime.includes('zip') || mime.includes('compressed')) return 'fa-file-zipper';
        return 'fa-file-lines';
    }

    function escapeHtml(value) {
        if (value === null || value === undefined) return '';
        return value
            .toString()
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function createTextBubble({ direction, sender, message, timestamp }) {
        const bubble = document.createElement('div');
        bubble.className = `message-bubble ${direction === 'out' ? 'outgoing' : 'incoming'}`;
        const meta = document.createElement('div');
        meta.className = 'message-meta';

        const senderSpan = document.createElement('span');
        senderSpan.className = 'message-sender';
        senderSpan.textContent = sender;

        const timeSpan = document.createElement('span');
        timeSpan.className = 'message-time';
        timeSpan.textContent = formatTimestamp(timestamp);

        const body = document.createElement('div');
        body.className = 'message-body';
        body.textContent = message;

        meta.appendChild(senderSpan);
        meta.appendChild(timeSpan);
        bubble.appendChild(meta);
        bubble.appendChild(body);
        return bubble;
    }

    function createStoredFileCard({ messageId, direction, sender, timestamp, fileName, fileType, fileUrl }) {
        const card = document.createElement('div');
        card.className = `card file-card shadow-sm mb-3 ${direction === 'out' ? 'outgoing' : 'incoming'}`;
        if (messageId) {
            card.dataset.messageId = messageId;
        }
        const safeName = escapeHtml(fileName);
        const safeSender = escapeHtml(sender);
        const safeTimestamp = escapeHtml(formatTimestamp(timestamp));
        const safeUrl = fileUrl ? escapeHtml(fileUrl) : '';
        const downloadButton = fileUrl
            ? `<a href="${safeUrl}" class="btn btn-sm btn-outline-primary" download title="Download">
                    <i class=\"fa-solid fa-download me-1\"></i>Download
               </a>`
            : '<span class="badge bg-secondary-subtle text-secondary">Unavailable</span>';
        card.innerHTML = `
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-3">
                        <i class="fa-solid ${fileTypeIcon(fileType)} fa-2x text-primary"></i>
                        <div>
                            <h5 class="card-title mb-1">${safeName}</h5>
                            <div class="text-secondary small">${safeSender} • ${safeTimestamp}</div>
                        </div>
                    </div>
                    ${downloadButton}
                </div>
                ${fileUrl ? `<div class="file-preview mt-3" data-preview="${safeUrl}"></div>` : ''}
            </div>
        `;
        return card;
    }

    function createWebRTCLog({ messageId, direction, sender, timestamp, fileName }) {
        const wrapper = document.createElement('div');
        wrapper.className = `webrtc-log ${direction === 'out' ? 'outgoing' : 'incoming'}`;
        if (messageId) {
            wrapper.dataset.messageId = messageId;
        }
        const safeName = escapeHtml(fileName);
        const safeSender = escapeHtml(sender);
        const safeTime = escapeHtml(formatTimestamp(timestamp));
        wrapper.innerHTML = `
            <i class="fa-solid fa-paperclip me-2"></i>
            <div>
                <div class="fw-semibold">${safeName}</div>
                <div class="small text-secondary">${safeSender} • ${safeTime} • Realtime transfer</div>
            </div>
        `;
        return wrapper;
    }

    function createTransferCard(meta, direction) {
        if (!dom.chatFeed) return null;
        const card = document.createElement('div');
        card.className = `card file-card shadow-sm mb-3 ${direction === 'out' ? 'outgoing' : 'incoming'}`;
        card.dataset.fileId = meta.fileId;
        const safeName = escapeHtml(meta.name);
        const safeSender = escapeHtml(meta.sender);
        const safeTime = escapeHtml(new Date(meta.timestamp).toLocaleTimeString());
        card.innerHTML = `
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-3">
                        <i class="fa-solid ${fileTypeIcon(meta.mime)} fa-2x text-primary"></i>
                        <div>
                            <h5 class="card-title mb-1">${safeName}</h5>
                            <div class="text-secondary small">${safeSender} • ${safeTime}</div>
                        </div>
                    </div>
                    <span class="badge ${direction === 'out' ? 'bg-primary-subtle text-primary' : 'bg-success-subtle text-success'}">
                        ${direction === 'out' ? 'Sending' : 'Receiving'}
                    </span>
                </div>
                <div class="progress mt-3" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar" style="width: 0%;">0%</div>
                </div>
                <div class="file-preview mt-3 d-none"></div>
            </div>
        `;
        dom.chatFeed.appendChild(card);
        scrollToBottom();
        return card;
    }

    function updateProgress(card, value) {
        if (!card) return;
        const progressBar = card.querySelector('.progress-bar');
        if (!progressBar) return;
        progressBar.style.width = `${value}%`;
        progressBar.textContent = `${value}%`;
        if (value >= 100) {
            progressBar.classList.add('bg-success');
        }
    }

    function renderPreview(card, file, mimeType) {
        if (!card) return;
        const preview = card.querySelector('.file-preview');
        if (!preview) return;

        const type = mimeType || (file && file.type) || 'application/octet-stream';
        if (!type.startsWith('image/')) {
            preview.innerHTML = '';
            preview.classList.add('d-none');
            return;
        }

        let element = null;

        if (file instanceof Blob) {
            const url = URL.createObjectURL(file);
            element = document.createElement('img');
            element.src = url;
            element.alt = file.name || 'preview';
        } else if (typeof file === 'string') {
            element = document.createElement('img');
            element.src = file;
            element.alt = 'preview';
        }

        if (element) {
            preview.innerHTML = '';
            preview.appendChild(element);
            preview.classList.remove('d-none');
        } else {
            preview.innerHTML = '';
            preview.classList.add('d-none');
        }
    }

    function updateModeStatusLabel() {
        if (!dom.modeStatus) return;
        const label = state.currentTransferMode === 'store' ? 'Server storage' : 'Realtime WebRTC';
        dom.modeStatus.innerHTML = `<i class="fa-solid fa-arrows-rotate me-1"></i>Transfer mode: ${label}`;
        if (dom.dropZoneText) {
            dom.dropZoneText.innerHTML = state.currentTransferMode === 'store'
                ? 'Drag & drop files here or <span class="text-primary">click to browse</span> to upload & store on the server'
                : 'Drag & drop files here or <span class="text-primary">click to browse</span> for realtime WebRTC transfer';
        }
    }

    function renderMessage(message, isOwn = false) {
        if (!dom.chatFeed || !message) return;
        const direction = isOwn || message.sender_id === state.currentUser.id ? 'out' : 'in';
        const senderName = message.display_name
            || (direction === 'out' ? state.currentUser.display_name : 'Participant');

        if (message.message_type === 'text') {
            const bubble = createTextBubble({
                direction,
                sender: senderName,
                message: message.message_text || '',
                timestamp: message.created_at
            });
            dom.chatFeed.appendChild(bubble);
            scrollToBottom();
            return;
        }

        if (message.message_type === 'file') {
            if (message.transfer_mode === 'store' && message.file_path) {
                const existing = message.id ? dom.chatFeed.querySelector(`[data-message-id="${message.id}"]`) : null;
                if (existing) return;
                const card = createStoredFileCard({
                    messageId: message.id,
                    direction,
                    sender: senderName,
                    timestamp: message.created_at,
                    fileName: message.file_name,
                    fileType: message.file_type,
                    fileUrl: message.file_path
                });
                dom.chatFeed.appendChild(card);
                if (message.file_path && message.file_type && message.file_type.startsWith('image/')) {
                    renderPreview(card, message.file_path, message.file_type);
                }
                scrollToBottom();
                return;
            }

            const log = createWebRTCLog({
                messageId: message.id,
                direction,
                sender: senderName,
                timestamp: message.created_at,
                fileName: message.file_name || 'File shared'
            });
            dom.chatFeed.appendChild(log);
            scrollToBottom();
        }
    }

    function renderHistory(historyItems) {
        historyItems.forEach((item) => {
            renderMessage(item, item.sender_id === state.currentUser.id);
        });
        scrollToBottom();
    }

    function renderUserList(users) {
        if (!Array.isArray(users) || !dom.userList || !dom.userCount) return;
        state.lastRoster = users;
        dom.userList.innerHTML = '';
        let onlineCount = 0;

        users.forEach((user) => {
            const isOnline = user.status === 'online';
            if (isOnline) {
                onlineCount += 1;
            } else if (window.RoomWebRTC && typeof window.RoomWebRTC.closePeer === 'function') {
                window.RoomWebRTC.closePeer(user.userId, { clearReconnect: true });
            }
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';
            const safeName = escapeHtml(user.displayName);
            li.innerHTML = `
                <span><i class="fa-solid fa-circle me-2 ${isOnline ? 'text-success' : 'text-secondary'}"></i>${safeName}</span>
                <span class="badge ${isOnline ? 'bg-primary-subtle text-primary' : 'bg-secondary-subtle text-secondary'}">${isOnline ? 'Online' : 'Offline'}</span>
            `;
            dom.userList.appendChild(li);
        });

        dom.userCount.textContent = onlineCount;

        if (window.RoomWebRTC && typeof window.RoomWebRTC.ensurePeerConnections === 'function'
            && window.RoomWebRTC.shouldUseWebRTC()) {
            window.RoomWebRTC.ensurePeerConnections(users);
        }
    }

    function showToast() {
        if (toastInstance) {
            toastInstance.show();
        }
    }

    window.RoomUI = {
        appendSystemMessage,
        createTransferCard,
        fileTypeIcon,
        renderHistory,
        renderMessage,
        renderPreview,
        renderUserList,
        scrollToBottom,
        showToast,
        updateModeStatusLabel,
        updateProgress
    };
})(window);
