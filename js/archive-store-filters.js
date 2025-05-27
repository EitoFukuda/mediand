jQuery(document).ready(function($) {
    console.log('[Archive Filters] Complete version loaded');

    // --- タブ切り替え機能 ---
    const $tabs = $('.store-search-tabs__item');
    const $filterPanels = $('.store-search-filter-panel');
    
    console.log('[Archive Filters] Found tabs:', $tabs.length, 'panels:', $filterPanels.length);

    // 初期表示設定
    function initializeTabs() {
        const urlParams = new URLSearchParams(window.location.search);
        let activeTab = urlParams.get('active_tab') || 'region';
        
        console.log('[Archive Filters] URL active_tab parameter:', activeTab);
        
        // 全てのタブとパネルをリセット
        $tabs.removeClass('is-active');
        $filterPanels.removeClass('is-active').hide();
        
        // アクティブなタブとパネルを表示
        const $activeTab = $tabs.filter(`[data-tab-target="#filter-${activeTab}"]`);
        const $activePanel = $(`#filter-${activeTab}`);
        
        if ($activeTab.length && $activePanel.length) {
            $activeTab.addClass('is-active');
            $activePanel.addClass('is-active').show();
            console.log('[Archive Filters] Active tab set to:', activeTab);
        } else {
            // フォールバック：最初のタブを表示
            const firstTab = $tabs.first();
            const firstPanel = $filterPanels.first();
            
            if (firstTab.length && firstPanel.length) {
                firstTab.addClass('is-active');
                firstPanel.addClass('is-active').show();
                
                const firstTabName = firstTab.data('tab-target')?.replace('#filter-', '') || 'region';
                $('.active-tab-input').val(firstTabName);
                console.log('[Archive Filters] Fallback to first tab:', firstTabName);
            }
        }
    }

    // タブクリックイベント
    $tabs.on('click', function(e) {
        e.preventDefault();
        
        const $clickedTab = $(this);
        const targetPanelId = $clickedTab.data('tab-target');
        
        console.log('[Archive Filters] Tab clicked:', $clickedTab.text().trim(), 'Target:', targetPanelId);
        
        if (!$clickedTab.hasClass('is-active')) {
            console.log('[Archive Filters] Switching to tab:', targetPanelId);
            
            // タブ切り替えアニメーション
            $tabs.removeClass('is-active');
            $filterPanels.removeClass('is-active').slideUp(200);
            
            // 新しいタブをアクティブに
            $clickedTab.addClass('is-active');
            $(targetPanelId).addClass('is-active').slideDown(300);
            
            // hidden inputを更新
            const tabName = targetPanelId.replace('#filter-', '');
            $('.active-tab-input').val(tabName);
            
            console.log('[Archive Filters] Tab switch completed:', tabName);
        }
    });

 // --- 地域フィルター機能 ---
 const $regionButtons = $('.filter-button--region');
 const $prefectureGroups = $('.filter-region__prefecture-group');
 const $prefecturesPlaceholder = $('.filter-region__prefectures-placeholder');
 
 console.log('[Archive Filters] Region setup - Buttons:', $regionButtons.length, 'Groups:', $prefectureGroups.length);
 
 // 地域ボタンクリックイベント
 $regionButtons.on('click', function() {
     const regionId = $(this).data('region-id');
     const $button = $(this);
     
     console.log('[Archive Filters] Region clicked:', regionId, 'Text:', $button.text().trim());
     
     // 他の地域ボタンの選択を解除
     $regionButtons.not($button).removeClass('is-selected');
     
     // 全ての都道府県グループを非表示
     $prefectureGroups.removeClass('active').slideUp(200);
     
     if ($button.hasClass('is-selected')) {
         // 同じ地域を再度クリックした場合は選択解除
         console.log('[Archive Filters] Deselecting region:', regionId);
         $button.removeClass('is-selected');
         $prefecturesPlaceholder.text('地方を選択してください').slideDown(200);
         
         // 対応する都道府県選択もクリア
         const $targetGroup = $(`[data-parent-region-id="${regionId}"]`);
         $targetGroup.find('input[type="radio"]').prop('checked', false);
         $targetGroup.find('.filter-button').removeClass('is-selected');
     } else {
         // 新しい地域を選択
         console.log('[Archive Filters] Selecting new region:', regionId);
         $button.addClass('is-selected');
         
         // 対象の都道府県グループのみ表示
         const $targetGroup = $(`[data-parent-region-id="${regionId}"]`);
         if ($targetGroup.length && $targetGroup.find('label').length) {
             $targetGroup.slideDown(300).addClass('active');
             $prefecturesPlaceholder.hide();
             console.log('[Archive Filters] Prefecture group displayed for region:', regionId);
         } else {
             $prefecturesPlaceholder.text('この地方の都道府県は登録されていません。').slideDown(200);
             console.log('[Archive Filters] No prefectures found for region:', regionId);
         }
     }
 });

    // --- フィルターボタンの選択状態管理（クリーン版） ---
    
    // ラジオボタン（都道府県選択）
    $('.filter-button--radio input[type="radio"]').on('change', function() {
        const name = $(this).attr('name');
        const value = $(this).val();
        
        console.log('[Archive Filters] Radio button changed:', name, '=', value);
        
        // 同じname属性のボタンの選択状態をリセット
        $(`.filter-button--radio input[name="${name}"]`).closest('.filter-button--radio').removeClass('is-selected');
        
        // 選択されたボタンのスタイルを適用
        if ($(this).is(':checked')) {
            $(this).closest('.filter-button--radio').addClass('is-selected');
            console.log('[Archive Filters] Radio selected:', value);
        }
    });

    // チェックボックス（その他フィルター）
    $('.filter-button--checkbox input[type="checkbox"]').on('change', function() {
        const $label = $(this).closest('.filter-button--checkbox');
        const isChecked = $(this).is(':checked');
        const value = $(this).val();
        
        // 選択状態のクラス切り替え（チェックマークなし、背景色のみ）
        $label.toggleClass('is-selected', isChecked);
        
        console.log('[Archive Filters] Checkbox changed:', value, 'Checked:', isChecked);
    });

    // ボタンクリック時の明示的な処理（チェックマーク防止）
    $('.filter-button--checkbox, .filter-button--radio').on('click', function(e) {
        // デフォルトのフォーム送信を防ぐ
        e.preventDefault();
        
        const $input = $(this).find('input');
        
        if ($input.length) {
            if ($input.attr('type') === 'radio') {
                // ラジオボタンの場合
                const name = $input.attr('name');
                $(`.filter-button input[name="${name}"]`).closest('.filter-button').removeClass('is-selected');
                $input.prop('checked', true);
                $(this).addClass('is-selected');
                
                // changeイベントをトリガー
                $input.trigger('change');
            } else if ($input.attr('type') === 'checkbox') {
                // チェックボックスの場合
                const isCurrentlyChecked = $input.is(':checked');
                $input.prop('checked', !isCurrentlyChecked);
                $(this).toggleClass('is-selected', !isCurrentlyChecked);
                
                // changeイベントをトリガー
                $input.trigger('change');
            }
        }
    });

    // --- 初期状態の選択スタイル適用 ---
    function applyInitialStyles() {
        $('.filter-button input:checked').each(function() {
            $(this).closest('.filter-button').addClass('is-selected');
            console.log('[Archive Filters] Initial selection applied:', $(this).val());
        });
    }

    // --- 地域・都道府県の初期状態設定 ---
    function setupInitialRegionState() {
        const $initialSelectedPref = $('input[name="prefecture_filter"]:checked');
        
        if ($initialSelectedPref.length) {
            const $initialGroup = $initialSelectedPref.closest('.filter-region__prefecture-group');
            const initialRegionId = $initialGroup.data('parent-region-id');
            
            if (initialRegionId) {
                console.log('[Archive Filters] Setting up initial region state:', initialRegionId);
                
                // 対応する地域ボタンを選択状態に
                $(`.filter-button--region[data-region-id="${initialRegionId}"]`).addClass('is-selected');
                
                // 都道府県グループを表示
                $initialGroup.show();
                $prefecturesPlaceholder.hide();
                
                console.log('[Archive Filters] Initial region state applied');
            }
        } else {
            // 初期状態では地方選択プレースホルダーを表示
            if ($regionButtons.length > 0 && $tabs.filter('[data-tab-target="#filter-region"]').hasClass('is-active')) {
                $prefecturesPlaceholder.show();
            }
        }
    }

    // --- リセットボタン ---
    $('.store-search-form__reset-button').on('click', function(e) {
        e.preventDefault();
        console.log('[Archive Filters] Reset button clicked');
        window.location.href = $(this).attr('href');
    });

    // --- フォーム送信時の処理 ---
    $('.store-search-form').on('submit', function() {
        console.log('[Archive Filters] Form submitted');
        
        // 選択された値をログ出力（デバッグ用）
        const selectedValues = {};
        
        $(this).find('input:checked, select').each(function() {
            const name = $(this).attr('name');
            const value = $(this).val();
            if (name && value) {
                if (!selectedValues[name]) {
                    selectedValues[name] = [];
                }
                selectedValues[name].push(value);
            }
        });
        
        console.log('[Archive Filters] Selected filter values:', selectedValues);
    });

    // --- 初期化実行 ---
    function initialize() {
        console.log('[Archive Filters] Starting initialization...');
        
        // タブ初期化
        initializeTabs();
        
        // 選択状態適用
        applyInitialStyles();
        
        // 地域状態設定
        setupInitialRegionState();
        
        console.log('[Archive Filters] Initialization completed');
    }

    // DOM準備完了後に初期化実行
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize);
    } else {
        initialize();
    }

    // --- デバッグ用：要素チェック ---
    console.log('[Archive Filters] Debug info:');
    console.log('- Tabs:', $tabs.length);
    console.log('- Panels:', $filterPanels.length);
    console.log('- Region buttons:', $regionButtons.length);
    console.log('- Prefecture groups:', $prefectureGroups.length);
    console.log('- Filter buttons total:', $('.filter-button').length);
    console.log('[Archive Filters] Script loaded successfully');
});