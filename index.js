document.addEventListener("DOMContentLoaded", function() {
    // Web Notifications
    function notifyUser(title, body) {
        if (!("Notification" in window)) return;
        if (Notification.permission === "granted") {
            new Notification(title, {body});
        } else if (Notification.permission !== "denied") {
            Notification.requestPermission().then(permission => {
                if (permission === "granted") {
                    new Notification(title, {body});
                }
            });
        }
    }

    // Back arrow navigation
    document.querySelector('.back-arrow a')?.addEventListener('click', function(e){
        if (window.history.length > 1) {
            e.preventDefault();
            window.history.back();
        }
    });

    // Search bar (instant filtering)
    const searchForm = document.getElementById('searchForm');
    if (searchForm) {
        searchForm.onsubmit = function(e) {
            e.preventDefault();
            const q = document.getElementById('searchInput').value.trim();
            window.location = "?search=" + encodeURIComponent(q);
        };
    }

    // Post form with spinner & notification
    const postForm = document.getElementById("postForm");
    if (postForm) {
        postForm.addEventListener("submit", function(e) {
            e.preventDefault();
            const btn = document.getElementById("postBtn");
            const spinner = document.getElementById("postSpinner");
            btn.disabled = true;
            spinner.classList.remove('d-none');
            const fd = new FormData(postForm);
            fd.append("action", "add_post");
            fetch("", {
                method: "POST",
                body: fd
            }).then(res => res.json()).then(data => {
                btn.disabled = false;
                spinner.classList.add('d-none');
                const status = document.getElementById("postStatus");
                if (data.error) {
                    status.innerHTML = `<div class="alert alert-danger">${data.error} <br>${data.summary??""}</div>`;
                    if (data.notify === 'banned') {
                        notifyUser("Forum Ban", "You are banned from posting for 3 days.");
                    } else if (data.notify === 'badword') {
                        notifyUser("Inappropriate Content", "Your post/comment was deleted due to bad words.");
                    }
                } else {
                    status.innerHTML = `<div class="alert alert-success">Posted!</div>`;
                    notifyUser("New Post", "Your post was successfully created.");
                    setTimeout(()=>location.reload(), 1000);
                }
            }).catch(()=>{
                btn.disabled = false;
                spinner.classList.add('d-none');
            });
        });
    }

    // Like buttons
    document.querySelectorAll(".like-btn").forEach(btn => {
        btn.addEventListener("click", function() {
            const id = btn.dataset.id;
            fetch("", {
                method: "POST",
                headers: {"X-Requested-With":"XMLHttpRequest"},
                body: new URLSearchParams({action:"like",forum_id:id})
            }).then(res=>res.json()).then(data=>{
                if (data.count !== undefined) {
                    btn.classList.toggle("liked", data.liked);
                    btn.querySelector(".like-count").textContent = data.count;
                }
            });
        });
        fetch(`api/forum_fetch.php?action=like_count&id=${btn.dataset.id}`)
        .then(res=>res.json()).then(data=>{
            btn.querySelector(".like-count").textContent = data.count ?? 0;
            if (data.liked) btn.classList.add("liked");
        });
    });

    // Comment toggles and pagination
    document.querySelectorAll(".comment-toggle-btn").forEach(btn => {
        btn.addEventListener("click", function() {
            const pid = btn.dataset.id;
            const sec = document.querySelector(`.forum-comments[data-id='${pid}']`);
            if (!sec) return;
            sec.classList.toggle("d-none");
            if (!sec.classList.contains("d-none") && !sec.dataset.loaded) {
                loadComments(pid, sec, 1);
            }
        });
    });

    // Comment forms (with notification)
    document.querySelectorAll(".commentForm").forEach(form => {
        form.addEventListener("submit", function(e) {
            e.preventDefault();
            const pid = form.dataset.forum;
            const fd = new FormData(form);
            fd.append("action", "add_comment");
            fd.append("forum_id", pid);
            fetch("", {
                method: "POST",
                body: fd
            }).then(res => res.json()).then(data => {
                if (data.error) {
                    alert(data.error + "\n" + (data.summary??""));
                    if (data.notify === 'banned') {
                        notifyUser("Forum Ban", "You are banned from posting for 3 days.");
                    } else if (data.notify === 'badword') {
                        notifyUser("Inappropriate Content", "Your post/comment was deleted due to bad words.");
                    }
                } else {
                    form.reset();
                    notifyUser("New Comment", "Your comment was posted.");
                    loadComments(pid, form.closest(".forum-comments"), 1, true);
                }
            });
        });
    });

    // Load comments (paginated, threaded)
    function loadComments(postId, container, page=1, scrollToEnd=false) {
        fetch(`api/forum_fetch.php?action=comments&forum_id=${postId}&page=${page}`)
        .then(res=>res.json()).then(data=>{
            const list = container.querySelector(".comment-list");
            if (page === 1) list.innerHTML = "";
            renderComments(data.comments, list, postId);
            const moreBtn = container.querySelector(".load-more-comments");
            if ((page*5) < data.total) {
                moreBtn.style.display = '';
                moreBtn.onclick = function() {
                    loadComments(postId, container, page+1);
                };
            } else {
                moreBtn.style.display = 'none';
            }
            container.dataset.loaded = "1";
            if (scrollToEnd) {
                container.scrollIntoView({behavior:'smooth',block:'end'});
            }
        });
    }

    function renderComments(comments, list, forumId) {
        comments.forEach((c) => {
            const div = document.createElement("div");
            div.className = "comment";
            div.innerHTML = `
                <div class="d-flex align-items-center gap-2">
                    <img src="${c.profile_image||'assets/img/default_user.png'}" class="rounded-circle" style="width:28px;height:28px;object-fit:cover;">
                    <span class="comment-author">${c.name||'Guest'}</span>
                    <span class="comment-date">${(c.created_at||'').slice(0,16).replace('T',' ')}</span>
                </div>
                <div>${c.content.replace(/\n/g,"<br>")}</div>
                <button class="reply-btn" data-id="${c.id}" type="button"><i class="bi bi-arrow-return-right"></i> Reply</button>
                <div class="reply-form d-none"></div>
            `;
            list.appendChild(div);

            // Reply button logic
            div.querySelector(".reply-btn").onclick = function() {
                const rf = div.querySelector(".reply-form");
                if (!rf.innerHTML) {
                    rf.innerHTML = `
                        <form class="replyCommentForm mt-2" data-forum="${forumId}" data-parent="${c.id}">
                            <textarea name="content" rows="1" class="form-control mb-1" required maxlength="300" placeholder="Reply..."></textarea>
                            <button type="submit" class="btn btn-sm main-btn">Reply</button>
                        </form>
                    `;
                    rf.querySelector("form").onsubmit = function(e) {
                        e.preventDefault();
                        const fd = new FormData(rf.querySelector("form"));
                        fd.append("action", "add_comment");
                        fd.append("forum_id", forumId);
                        fd.append("parent_comment_id", c.id);
                        fetch("", {
                            method: "POST",
                            body: fd
                        }).then(res => res.json()).then(data => {
                            if (data.error) {
                                alert(data.error + "\n" + (data.summary??""));
                            } else {
                                rf.innerHTML = "";
                                loadComments(forumId, list.closest(".forum-comments"), 1, true);
                            }
                        });
                    }
                }
                rf.classList.toggle("d-none");
            };
        });
    }

    // AI summary toggle (show/hide and load only on click, with online icon and spinner)
    document.querySelectorAll('.ai-summary-toggle').forEach(icon=>{
        icon.addEventListener('click', function(){
            const postId = icon.dataset.id;
            const postDiv = icon.closest('.forum-post');
            const box = postDiv.querySelector('.ai-summary-box');
            // Only fetch if not already loaded
            if (!box.dataset.loaded) {
                box.style.display = "block";
                box.innerHTML = `<span class="spinner-border spinner-border-sm text-success me-2"></span> Loading summary...`;
                let content = postDiv.querySelector('.mb-1').innerText;
                fetch("", {
                    method: "POST",
                    headers: {"X-Requested-With":"XMLHttpRequest"},
                    body: new URLSearchParams({action:"ai_summary",content:content})
                }).then(res=>res.json()).then(data=>{
                    if (data.summary && data.summary.trim() !== "") {
                        box.innerHTML = `<i class="bi bi-robot"></i> ${data.summary}`;
                    } else {
                        box.innerHTML = `<span class="text-danger"><i class="bi bi-x-circle"></i> Failed to load summary.</span>`;
                    }
                    box.dataset.loaded = 1;
                }).catch(()=>{
                    box.innerHTML = `<span class="text-danger"><i class="bi bi-x-circle"></i> Error loading summary.</span>`;
                    box.dataset.loaded = 1;
                });
            } else {
                // toggle visibility for this box only
                box.style.display = (box.style.display==='block'?'none':'block');
            }
        });
    });

    // Post deletion: double click to show delete for owner
    document.querySelectorAll('.forum-post').forEach(post=>{
        post.addEventListener('dblclick', function(){
            if (parseInt(post.dataset.owner) === window.forumUserId) {
                post.classList.add('deletable');
                post.querySelector('.delete-btn').style.display = 'inline-block';
            }
        });
        post.querySelector('.delete-btn').addEventListener('click', function(){
            if (confirm('Are you sure you want to delete this post?')) {
                fetch("", {
                    method:"POST",
                    body:new URLSearchParams({action:"delete_post",post_id:post.dataset.id})
                }).then(res=>res.json()).then(data=>{
                    if (data.success) {
                        post.remove();
                        notifyUser("Post Deleted", "Your post was deleted.");
                    } else {
                        alert(data.error||'Could not delete');
                    }
                });
            }
        });
    });
});