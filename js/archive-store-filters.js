jQuery(document).ready(function($) {

    // --- タブ切り替え機能 ---
    const $tabs = $('.store-search-tabs__item');
    const $filterPanels = $('.store-search-filter-panel');
    console.log('[Archive Filters] Tabs found:', $tabs.length);
    console.log('[Archive Filters] Filter panels found:', $filterPanels.length);

    // 初期表示時のアクティブタブとパネルを設定
    // URLにアクティブなタブを示すパラメータがあればそれを優先、なければ最初のタブ
    const urlParams = new URLSearchParams(window.location.search);
    let activeTabFromUrl = urlParams.get('active_tab');

    // URLにactive_tabパラメータがない場合、または該当するタブがない場合は、最初のタブをデフォルトにする
    if (!activeTabFromUrl || $tabs.filter('[data-tab-target="#filter-' + activeTabFromUrl + '"]').length === 0) {
        if ($tabs.length > 0) {
            activeTabFromUrl = $tabs.first().data('tab-target').substring(1); // #filter-region から filter-region を取得
        }
    }

    if (activeTabFromUrl && $tabs.length > 0 && $filterPanels.length > 0) {
        $tabs.removeClass('is-active');
        $filterPanels.removeClass('is-active').hide(); // 一旦すべて隠す

        $tabs.filter('[data-tab-target="#filter-' + activeTabFromUrl + '"]').addClass('is-active');
        $('#filter-' + activeTabFromUrl).addClass('is-active').show(); // URLまたは最初のタブに対応するパネルを表示
    }


    $tabs.on('click', function(event) {
        event.preventDefault();
        console.log('[Archive Filters] Tab clicked:', $(this).text().trim()); // クリックされたタブのテキスト
        const targetPanelId = $(this).data('tab-target');
        console.log('[Archive Filters] Target panel ID:', targetPanelId);

        if (!$(this).hasClass('is-active')) {
            console.log('[Archive Filters] Clicked tab is not active. Processing...');
            $tabs.removeClass('is-active');
            $filterPanels.removeClass('is-active').slideUp(200);
            console.log('[Archive Filters] All tabs and panels deactivated.');

            $(this).addClass('is-active');
            $(targetPanelId).addClass('is-active').slideDown(300);
            console.log('[Archive Filters] Clicked tab and target panel activated:', targetPanelId);

            // 新しいURLパラメータを生成（オプション）
            const newUrlParams = new URLSearchParams(window.location.search);
            newUrlParams.set('active_tab', targetPanelId.substring(1)); // #を除いたID
            // history.pushState(null, '', window.location.pathname + '?' + newUrlParams.toString()); // URLを書き換える場合（ページリロードなし）
        }
    });

    // --- 「地域から選ぶ」タブ内の地方→都道府県 連動表示機能 ---
    const $regionButtons = $('.filter-button.filter-button--region');
    const $prefectureGroups = $('.filter-region__prefecture-group');
    const $prefecturesPlaceholder = $('.filter-region__prefectures-placeholder');
    console.log('[Archive Filters] Region buttons found:', $regionButtons.length); // filter-button--region にクラス名を変更したためセレクタ修正

    $regionButtons.on('click', function() {
        const selectedRegionId = $(this).data('region-id');
        console.log('[Archive Filters] Region button clicked. Region ID:', selectedRegionId, 'Selected text:', $(this).text().trim());

        if ($(this).hasClass('is-selected')) {
            console.log('[Archive Filters] Region button already selected. Deselecting...');
            // 再度クリックされた場合は選択解除（都道府県もリセット）
            $(this).removeClass('is-selected');
            $prefectureGroups.slideUp(200);
            $prefecturesPlaceholder.text('地方を選択してください').slideDown(200);
            // 対応するラジオボタンの選択も解除
            $('.filter-region__prefecture-group[data-parent-region-id="' + selectedRegionId + '"] input[type="radio"]').prop('checked', false);
            $('.filter-region__prefecture-group[data-parent-region-id="' + selectedRegionId + '"] label.is-selected').removeClass('is-selected');
            console.log('[Archive Filters] Radio button for prefecture changed.');
            const currentName = $(this).attr('name');
            $('.filter-button--radio input[name="' + currentName + '"]').closest('.filter-button--radio').removeClass('is-selected');
            if ($(this).is(':checked')) {
                $(this).closest('.filter-button--radio').addClass('is-selected');
                console.log('[Archive Filters] Radio button label selected:', $(this).closest('label').text().trim());
            }
        } else {
            $regionButtons.removeClass('is-selected');
            $(this).addClass('is-selected');
            $prefectureGroups.slideUp(200); // 他の都道府県グループを隠す
            $prefecturesPlaceholder.slideUp(200);

            const targetPrefGroup = $prefectureGroups.filter('[data-parent-region-id="' + selectedRegionId + '"]');
            if (targetPrefGroup.length > 0 && targetPrefGroup.find('label').length > 0) { // 都道府県が存在する場合のみ表示
                targetPrefGroup.slideDown(300);
            } else if (targetPrefGroup.length > 0 && targetPrefGroup.find('label').length === 0) {
                 $prefecturesPlaceholder.text('この地方の都道府県は登録されていません。').slideDown(200);
            } else {
                $prefecturesPlaceholder.text('都道府県が見つかりません。').slideDown(200);
            }
        }
    });

    // ページ読み込み時に、選択されている都道府県があれば対応する地方を開く
    const initialSelectedPrefRadio = $('input[name="prefecture_filter"]:checked');
    if (initialSelectedPrefRadio.length > 0) {
        const initialPrefGroup = initialSelectedPrefRadio.closest('.filter-region__prefecture-group');
        const initialRegionId = initialPrefGroup.data('parent-region-id');
        if (initialRegionId) {
            $regionButtons.filter('[data-region-id="' + initialRegionId + '"]').addClass('is-selected'); // trigger('click') は不要かも
            initialPrefGroup.show();
            $prefecturesPlaceholder.hide();
        }
    } else if ($regionButtons.length > 0 && $tabs.filter('[data-tab-target="#filter-region"]').hasClass('is-active')) {
        $prefecturesPlaceholder.show();
    }

    // --- フィルターボタン（チェックボックス/ラジオボタンのラベル）の選択状態の見た目制御 ---
    // ラジオボタン
    $('.filter-button--radio input[type="radio"]').on('change', function() {
        const currentName = $(this).attr('name');
        $('.filter-button--radio input[name="' + currentName + '"]').closest('.filter-button--radio').removeClass('is-selected');
        if ($(this).is(':checked')) {
            $(this).closest('.filter-button--radio').addClass('is-selected');
        }
    });

    // チェックボックス
    $('.filter-button--checkbox input[type="checkbox"]').on('change', function() {
        const $label = $(this).closest('.filter-button--checkbox');
        $label.toggleClass('is-selected', $(this).is(':checked'));
        console.log('[Archive Filters] Checkbox changed. Label:', $label.text().trim(), 'Checked:', $(this).is(':checked'));
    });

    // ページ読み込み時に、既にチェックされているもののスタイルを適用 (PHP側でクラス付与も行っているが念のため)
    $('.filter-button input[type="checkbox"]:checked, .filter-button input[type="radio"]:checked').each(function() {
        $(this).closest('.filter-button').addClass('is-selected');
    });

    // --- 条件リセットボタン ---
    $('.store-search-form__reset-button').on('click', function(event) {
        event.preventDefault();
        window.location.href = $(this).attr('href'); // パラメータなしのアーカイブページURLへ遷移
    });
});