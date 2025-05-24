/* 店舗詳細ページ修正版CSS */

/* --- ヒーローセクション --- */
.store-hero {
    background-size: cover;
    background-position: center center;
    background-repeat: no-repeat;
    color: #fff;
    position: relative;
    min-height: 500px;
    display: flex;
    align-items: flex-end;
    padding: 60px 0;
    margin-bottom: 60px;
}

.store-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.3) 50%, rgba(0,0,0,0) 100%);
    z-index: 1;
}

.store-hero.no-background-image {
    background: linear-gradient(135deg, #E8B4E8, #C77DC7);
    min-height: 300px;
    align-items: center;
}

.store-hero.no-background-image::before {
    display: none;
}

.store-hero__content {
    position: relative;
    z-index: 2;
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
}

.store-hero__main-info {
    flex: 1;
}

.store-hero__name {
    font-size: 36px;
    font-weight: bold;
    margin-bottom: 12px;
    line-height: 1.3;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.6);
}

.store-hero.no-background-image .store-hero__name {
    text-shadow: none;
}

.store-hero__location {
    font-size: 16px;
    display: flex;
    align-items: center;
    margin-bottom: 0;
}

.store-hero__location-icon {
    width: 16px;
    height: 16px;
    margin-right: 8px;
    filter: brightness(0) invert(1);
}

.store-hero.no-background-image .store-hero__location-icon {
    filter: none;
}

.store-hero__social-wrapper {
    flex-shrink: 0;
    margin-left: 30px;
}

.store-hero__social {
    display: flex;
    gap: 12px;
}

.store-hero__social-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background-color: rgba(255, 255, 255, 0.9);
    border-radius: 50%;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.store-hero__social-link:hover {
    background-color: #fff;
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
}

.store-hero__social-icon {
    width: 20px;
    height: 20px;
}

/* --- メインコンテンツ --- */
.store-content-wrapper {
    background-color: #f8f9fa;
    padding: 0 0 80px 0;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.store-main-content {
    margin-bottom: 80px;
}

.store-main-content__inner {
    display: flex;
    gap: 40px;
    align-items: flex-start;
}

/* --- 左カラム --- */
.store-main-content__left {
    flex: 1;
    max-width: 60%;
}

.store-recommended-photo {
    margin-bottom: 30px;
}

.store-recommended-photo__image {
    width: 100%;
    height: auto;
    border-radius: 16px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.store-recommended-photo__image:hover {
    transform: scale(1.02);
}

.store-catchphrase__title {
    font-size: 24px;
    font-weight: 700;
    color: #333;
    margin-bottom: 20px;
    position: relative;
    padding-bottom: 12px;
}

.store-catchphrase__title::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 60px;
    height: 3px;
    background: linear-gradient(to right, #E8B4E8, #C77DC7);
    border-radius: 2px;
}

.store-catchphrase__content {
    font-size: 16px;
    line-height: 1.8;
    color: #555;
}

.store-catchphrase__content p {
    margin-bottom: 1em;
}

.store-catchphrase__content p:last-child {
    margin-bottom: 0;
}

/* --- 右カラム --- */
.store-main-content__right {
    flex: 0 0 360px;
}

.store-info-card {
    background: #fff;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.08);
    position: sticky;
    top: 100px;
    border: 1px solid #e9ecef;
}

.store-info-card__reservation {
    margin-bottom: 30px;
    padding-bottom: 25px;
    border-bottom: 2px solid #f1f3f4;
}

.store-info-card__title {
    font-size: 14px;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 15px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.store-reservation-btn {
    display: block;
    background: linear-gradient(135deg, #E8B4E8, #C77DC7);
    color: #fff;
    text-align: center;
    padding: 16px 24px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    font-size: 16px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(199, 125, 199, 0.3);
}

.store-reservation-btn:hover {
    background: linear-gradient(135deg, #dda3dd, #b86cb8);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(199, 125, 199, 0.4);
    color: #fff;
}

.store-info-item {
    margin-bottom: 25px;
}

.store-info-item:last-child {
    margin-bottom: 0;
}

.store-info-item__title {
    font-size: 14px;
    font-weight: 700;
    color: #333;
    margin-bottom: 8px;
    position: relative;
    padding-bottom: 5px;
}

.store-info-item__title::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 30px;
    height: 2px;
    background: linear-gradient(to right, #E8B4E8, #C77DC7);
    border-radius: 1px;
}

.store-info-item__content {
    font-size: 14px;
    line-height: 1.6;
    color: #555;
    margin: 0;
}

.payment-methods-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.payment-methods-list li {
    padding: 2px 0;
}

/* --- 基本情報セクション --- */
.store-basic-info,
.store-map,
.store-related {
    background: #fff;
    border-radius: 20px;
    padding: 40px;
    margin-bottom: 40px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    border: 1px solid #e9ecef;
}

.store-section-title {
    font-size: 28px;
    font-weight: 700;
    color: #333;
    margin-bottom: 30px;
    position: relative;
    padding-bottom: 15px;
}

.store-section-title::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 80px;
    height: 4px;
    background: linear-gradient(to right, #E8B4E8, #C77DC7);
    border-radius: 2px;
}

.store-details-table {
    margin: 0;
}

.store-details-row {
    display: flex;
    padding: 20px 0;
    border-bottom: 1px solid #f1f3f4;
}

.store-details-row:last-child {