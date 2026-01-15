(function ($) {
    'use strict';

    $(function () {
        console.log('Houzi AI: Admin JS Loaded - VERSION 1.0.2');

        var $progressBar = $('#houzi-ai-progress-bar-fill');
        var $progressContainer = $('#houzi-ai-progress-container');
        var $progressText = $('#houzi-ai-progress-text');
        var $log = $('#houzi-ai-log');

        var totalPosts = 0;
        var processedPosts = 0;
        var postIds = [];
        var currentPostType = 'property';
        var isProcessing = false;

        function updateProgress(current, total) {
            var percentage = total === 0 ? 0 : (current / total) * 100;
            $progressBar.css('width', percentage + '%');
            $progressText.text(current + '/' + total);
        }

        console.log('Houzi AI: Binding click events to .houzi-bulk-generate-btn (v1.0.2)');

        // Direct event listener on the document body to catch bubbles early
        $(document).off('click', '.houzi-bulk-generate-btn').on('click', '.houzi-bulk-generate-btn', function (e) {
            // Prevent default immediately
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();

            if (isProcessing) {
                console.log('Houzi AI: Processing in progress. Ignoring additional click.');
                return;
            }

            var $btn = $(this);
            currentPostType = $btn.data('type');
            console.log('Houzi AI: Start click handler for type:', currentPostType);

            var scopeInput = $('input[name="houzi_ai_' + currentPostType + '_scope"]:checked');
            var scope = scopeInput.length ? scopeInput.val() : 'all';

            var typeLabel = 'Items';
            if (currentPostType === 'property') typeLabel = 'Properties';
            else if (currentPostType === 'houzez_agent') typeLabel = 'Agents';
            else if (currentPostType === 'houzez_agency') typeLabel = 'Agencies';

            var confirmMsg = "Are you sure you want to generate descriptions for " + (scope === 'all' ? "ALL" : "Pending") + " " + typeLabel + "?\n\nThis process may take some time.";

            // Calling confirm IMMEDIATELY in the same thread
            console.log('Houzi AI: Triggering confirm() dialog now...');
            var userConfirmed = confirm(confirmMsg);

            if (!userConfirmed) {
                console.log('Houzi AI: User clicked Cancel or dialog was dismissed.');
                return;
            }

            console.log('Houzi AI: User clicked OK. Starting AJAX flow.');
            isProcessing = true;
            $btn.prop('disabled', true);
            $progressContainer.show();
            $('#houzi-ai-current-type-label').text(typeLabel);
            $log.empty().append('<p>Starting generation for ' + typeLabel + '...</p>');
            updateProgress(0, 0);

            $.ajax({
                url: houzi_ai_obj.ajax_url,
                type: 'POST',
                data: {
                    action: 'houzi_get_total_posts',
                    nonce: houzi_ai_obj.nonce,
                    post_type: currentPostType,
                    scope: scope
                },
                success: function (response) {
                    console.log('Houzi AI: Initial fetch successful', response);
                    if (response.success) {
                        totalPosts = response.data.total;
                        postIds = response.data.ids;
                        processedPosts = 0;

                        if (totalPosts === 0) {
                            $log.append('<p>No ' + typeLabel.toLowerCase() + ' found to process.</p>');
                            $btn.prop('disabled', false);
                            isProcessing = false;
                            return;
                        }

                        updateProgress(0, totalPosts);
                        processNextPost($btn);
                    } else {
                        console.error('Houzi AI: Initial fetch error', response.data);
                        alert(response.data || 'Failed to fetch posts.');
                        $btn.prop('disabled', false);
                        isProcessing = false;
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Houzi AI: Initial fetch AJAX error', status, error);
                    alert('Connection error. Please try again.');
                    $btn.prop('disabled', false);
                    isProcessing = false;
                }
            });
        });

        function processNextPost($btn) {
            if (processedPosts >= totalPosts) {
                console.log('Houzi AI: All items processed.');
                $log.prepend('<p><strong>Bulk generation complete!</strong></p>');
                $btn.prop('disabled', false);
                isProcessing = false;
                return;
            }

            var postId = postIds[processedPosts];
            console.log('Houzi AI: Generating for post ID', postId, (processedPosts + 1) + '/' + totalPosts);

            $.ajax({
                url: houzi_ai_obj.ajax_url,
                type: 'POST',
                data: {
                    action: 'houzi_generate_description',
                    nonce: houzi_ai_obj.nonce,
                    post_id: postId
                },
                success: function (response) {
                    processedPosts++;
                    updateProgress(processedPosts, totalPosts);

                    if (response.success) {
                        $log.prepend('<p style="color: green;">✔ ' + response.data.message + '</p>');
                    } else {
                        console.error('Houzi AI: Item generation error', response.data);
                        $log.prepend('<p style="color: red;">✘ Error: ' + response.data + '</p>');
                    }

                    processNextPost($btn);
                },
                error: function (xhr, status, error) {
                    console.error('Houzi AI: Item generation AJAX error', status, error);
                    processedPosts++;
                    updateProgress(processedPosts, totalPosts);
                    $log.prepend('<p style="color: red;">✘ Network error for ID: ' + postId + '</p>');
                    processNextPost($btn);
                }
            });
        }

        // Single Post Generation
        var $singleBtn = $('#houzi-generate-single-btn');
        var $singleStatus = $('#houzi-ai-single-status');

        if ($singleBtn.length) {
            $singleBtn.on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                var post_id = houzi_ai_obj.post_id;
                if (!post_id) {
                    alert('Post ID not found.');
                    return;
                }

                $singleBtn.prop('disabled', true).text('Generating...');
                $singleStatus.html('<span style="color: blue;">AI is writing... please wait.</span>');

                $.ajax({
                    url: houzi_ai_obj.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'houzi_generate_description',
                        post_id: post_id,
                        nonce: houzi_ai_obj.nonce
                    },
                    success: function (response) {
                        if (response.success) {
                            var description = response.data.description;
                            var updated = false;

                            // Try Gutenberg (Block Editor)
                            try {
                                if (window.wp && wp.data && typeof wp.data.dispatch === 'function' && wp.data.dispatch('core/editor')) {
                                    wp.data.dispatch('core/editor').editPost({ content: description });
                                    updated = true;
                                }
                            } catch (err) {
                                console.error('Houzi AI: Gutenberg update failed', err);
                            }

                            // Try Classic Editor (TinyMCE)
                            if (!updated && window.tinymce && tinymce.get('content')) {
                                try {
                                    tinymce.get('content').setContent(description);
                                    updated = true;
                                } catch (err) {
                                    console.error('Houzi AI: TinyMCE update failed', err);
                                }
                            }

                            // Try plain textarea fallback
                            if (!updated && $('#content').length) {
                                $('#content').val(description);
                                updated = true;
                            }

                            if (updated) {
                                $singleStatus.html('<span style="color: green;">Success! Description inserted into editor. Please click "Update" or "Save" to apply changes permanently.</span>');
                            } else {
                                $singleStatus.html('<span style="color: green;">Success! Description updated in database. <a href="javascript:location.reload();">Refresh page</a> to see it in the editor.</span>');
                            }
                            $singleBtn.text('Regenerate AI Description');
                        } else {
                            $singleStatus.html('<span style="color: red;">Error: ' + response.data + '</span>');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Houzi AI: Single generation AJAX error:', status, error);
                        $singleStatus.html('<span style="color: red;">AJAX error occurred.</span>');
                    },
                    complete: function () {
                        $singleBtn.prop('disabled', false);
                        if ($singleBtn.text() === 'Generating...') {
                            $singleBtn.text('Generate AI Description');
                        }
                    }
                });
            });
        }
    });

})(jQuery);
