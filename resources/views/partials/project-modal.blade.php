<div id="project-modal" class="pm-modal" role="dialog" aria-modal="true" aria-labelledby="pm-title">
    <div class="pm-content">
        <h2 id="pm-title">Create New Project</h2>

        <form id="pm-form">
            <label for="pm-name">Project name</label>
            <input type="text" id="pm-name" name="name" required maxlength="{{ \App\Models\Project::NAME_MAX_LENGTH }}">
            <div class="pm-error" id="pm-error"></div>

            <div class="pm-actions">
                <button type="button" id="pm-cancel" class="pm-btn">Cancel</button>
                <button type="submit" class="pm-btn pm-btn-primary">Create Project</button>
            </div>
        </form>
    </div>
</div>

<style>
    .pm-modal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(17, 24, 39, .55);
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }
    .pm-content {
        background: #fff;
        border-radius: .5rem;
        padding: 1.5rem;
        max-width: 420px;
        width: 90%;
        box-shadow: 0 20px 60px rgba(0,0,0,.3);
    }
    .pm-content h2 { margin: 0 0 .75rem; }
    .pm-content label { display: block; font-weight: 600; margin-bottom: .25rem; }
    .pm-content input {
        width: 100%; padding: .5rem; border: 1px solid #d1d5db;
        border-radius: .375rem; box-sizing: border-box;
    }
    .pm-error { color: #b91c1c; font-size: .85rem; min-height: 1.1rem; margin-top: .35rem; }
    .pm-actions { display: flex; justify-content: flex-end; gap: .5rem; margin-top: 1rem; }
    .pm-btn {
        padding: .45rem .9rem; border-radius: .375rem; cursor: pointer;
        border: 1px solid #d1d5db; background: #fff; font-size: .9rem;
    }
    .pm-btn-primary { background: #2563eb; color: #fff; border-color: #2563eb; }
</style>

<script>
(function () {
    const csrfMeta   = document.querySelector('meta[name="csrf-token"]');
    const csrfToken  = csrfMeta ? csrfMeta.getAttribute('content') : '';
    const storeUrl   = '{{ route(\App\Http\Controllers\ProjectController::ROUTE_STORE) }}';
    const modal      = document.getElementById('project-modal');
    const form       = document.getElementById('pm-form');
    const input      = document.getElementById('pm-name');
    const errorEl    = document.getElementById('pm-error');
    const cancelBtn  = document.getElementById('pm-cancel');

    let targetSelectId = null;

    function close() {
        modal.style.display = 'none';
        form.reset();
        errorEl.textContent = '';
        targetSelectId = null;
    }

    window.openProjectModal = function (selectId) {
        targetSelectId = selectId || null;
        modal.style.display = 'flex';
        input.focus();
    };

    cancelBtn.addEventListener('click', close);

    modal.addEventListener('click', function (e) {
        if (e.target === modal) close();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.style.display === 'flex') close();
    });

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        errorEl.textContent = '';

        const name = input.value.trim();
        if (!name) {
            errorEl.textContent = 'Project name is required.';
            return;
        }

        try {
            const res = await fetch(storeUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ name: name }),
            });

            if (!res.ok) {
                const err = await res.json().catch(function () { return {}; });
                const firstFieldError =
                    err.errors && err.errors.name ? err.errors.name[0] : null;
                errorEl.textContent = firstFieldError || err.message || 'Could not create project.';
                return;
            }

            const payload = await res.json();
            const project = payload.data;

            document.querySelectorAll('select[name="project_id"]').forEach(function (sel) {
                const opt = document.createElement('option');
                opt.value = project.id;
                opt.textContent = project.name;
                sel.appendChild(opt);
            });

            if (targetSelectId) {
                const target = document.getElementById(targetSelectId);
                if (target) {
                    target.value = String(project.id);
                }
            }

            close();
        } catch (err) {
            console.error(err);
            errorEl.textContent = 'Network error – please try again.';
        }
    });
})();
</script>