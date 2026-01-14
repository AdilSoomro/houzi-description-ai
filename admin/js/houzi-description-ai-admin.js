(function ($) {
    'use strict';

    $(function () {
        var $btn = $('#houzi-generate-btn');
        var $progressContainer = $('#houzi-ai-progress-container');
        var $progressBar = $('#houzi-ai-progress-bar-fill');
        var $progressText = $('#houzi-ai-progress-text');
        var $log = $('#houzi-ai-log');

        $btn.on('click', function (e) {
            e.preventDefault();

            var scope = $('input[name="houzi_ai_scope"]:checked').val();
            var confirmMsg = scope === 'all'
                ? 'Are you sure you want to generate descriptions for ALL published properties?'
                : 'Are you sure you want to generate descriptions for properties without AI content?';

            if (!confirm(confirmMsg)) {
                return;
            }

            $btn.prop('disabled', true).text('Processing...');
            $progressContainer.show();
            $log.empty().append('<div>Starting process...</div>');
            $progressBar.css('width', '0%');
            $progressText.text('0/0');

            // Step 1: Get property IDs based on scope
            $.ajax({
                url: houzi_ai_obj.ajax_url,
                type: 'POST',
                data: {
                    action: 'houzi_get_total_properties',
                    scope: scope,
                    nonce: houzi_ai_obj.nonce
                },
                success: function (response) {
                    if (response.success) {
                        var ids = response.data.ids;
                        var total = response.data.total;

                        if (total === 0) {
                            $log.append('<div>No properties found.</div>');
                            $btn.prop('disabled', false).text('Generate Property Description');
                            return;
                        }

                        $progressText.text('0/' + total);
                        processProperties(ids, 0, total);
                    } else {
                        $log.append('<div style="color:red;">Error: ' + response.data + '</div>');
                        $btn.prop('disabled', false).text('Generate Property Description');
                    }
                },
                error: function () {
                    $log.append('<div style="color:red;">AJAX error while fetching properties.</div>');
                    $btn.prop('disabled', false).text('Generate Property Description');
                }
            });
        });

        function processProperties(ids, index, total) {
            if (index >= total) {
                $log.append('<div style="color:green; font-weight:bold;">Process completed!</div>');
                $btn.prop('disabled', false).text('Generate Property Description');
                return;
            }

            var post_id = ids[index];
            $log.append('<div>Processing Property ID: ' + post_id + '...</div>');
            $log.scrollTop($log[0].scrollHeight);

            $.ajax({
                url: houzi_ai_obj.ajax_url,
                type: 'POST',
                data: {
                    action: 'houzi_generate_description',
                    post_id: post_id,
                    nonce: houzi_ai_obj.nonce
                },
                success: function (response) {
                    var current = index + 1;
                    var progress = (current / total) * 100;
                    $progressBar.css('width', progress + '%');
                    $progressText.text(current + '/' + total);

                    if (response.success) {
                        $log.append('<div style="color:green;">' + response.data + '</div>');
                    } else {
                        $log.append('<div style="color:red;">Error for ID ' + post_id + ': ' + response.data + '</div>');
                    }
                },
                error: function () {
                    $log.append('<div style="color:red;">AJAX error for ID ' + post_id + '</div>');
                },
                complete: function () {
                    processProperties(ids, index + 1, total);
                }
            });
        }
    });

})(jQuery);
