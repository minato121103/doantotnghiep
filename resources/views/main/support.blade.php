@extends('layouts.main')

@section('title', 'Trung tâm hỗ trợ')

@push('styles')
<style>
    /* ========== SUPPORT PAGE STYLES ========== */
    .support-hero {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 30%, #4338ca 60%, #6366f1 100%);
        position: relative;
        overflow: hidden;
        padding: 140px 0 80px;
    }
    .support-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .support-hero::after {
        content: '';
        position: absolute;
        bottom: 0; left: 0; right: 0;
        height: 120px;
        background: linear-gradient(to top, #f8fafc, transparent);
    }
    .hero-content { position: relative; z-index: 2; }


    /* Category Cards */
    .sp-categories {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-top: -40px;
        position: relative;
        z-index: 5;
    }
    .sp-cat-card {
        background: white;
        border-radius: 16px;
        padding: 28px 24px;
        border: 1px solid #e2e8f0;
        cursor: pointer;
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    .sp-cat-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
        background: var(--cat-color, #6366f1);
        transform: scaleX(0);
        transition: transform 0.35s;
    }
    .sp-cat-card:hover::before { transform: scaleX(1); }
    .sp-cat-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.08);
        border-color: transparent;
    }
    .sp-cat-icon {
        width: 56px; height: 56px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 16px;
        font-size: 26px;
        transition: all 0.35s;
    }
    .sp-cat-card:hover .sp-cat-icon { transform: scale(1.1); }
    .sp-cat-title { font-weight: 600; font-size: 15px; color: #1e293b; margin-bottom: 6px; }
    .sp-cat-desc { font-size: 13px; color: #64748b; line-height: 1.5; }

    /* FAQ Section */
    .sp-faq-item {
        background: white;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        transition: all 0.3s;
        margin-bottom: 10px;
    }
    .sp-faq-item:hover { border-color: #c7d2fe; }
    .sp-faq-item.active { border-color: #6366f1; box-shadow: 0 4px 20px rgba(99,102,241,0.1); }
    .sp-faq-q {
        padding: 18px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        gap: 16px;
        user-select: none;
    }
    .sp-faq-q span { font-weight: 500; font-size: 15px; color: #1e293b; flex: 1; }
    .sp-faq-q .sp-faq-icon {
        width: 28px; height: 28px;
        border-radius: 8px;
        background: #f1f5f9;
        display: flex; align-items: center; justify-content: center;
        transition: all 0.3s;
        flex-shrink: 0;
    }
    .sp-faq-item.active .sp-faq-icon { background: #6366f1; color: white; transform: rotate(180deg); }
    .sp-faq-a {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .sp-faq-item.active .sp-faq-a { max-height: 500px; }
    .sp-faq-a-inner {
        padding: 0 24px 20px;
        color: #475569;
        font-size: 14px;
        line-height: 1.7;
        border-top: 1px solid #f1f5f9;
        padding-top: 16px;
    }

    /* Contact Cards */
    .sp-contact-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
    }
    .sp-contact-card {
        background: white;
        border-radius: 16px;
        padding: 32px 28px;
        border: 1px solid #e2e8f0;
        text-align: center;
        transition: all 0.35s;
        position: relative;
    }
    .sp-contact-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 32px rgba(0,0,0,0.06);
        border-color: transparent;
    }
    .sp-contact-icon {
        width: 64px; height: 64px;
        border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 20px;
        font-size: 28px;
    }
    .sp-contact-title { font-weight: 600; font-size: 16px; color: #1e293b; margin-bottom: 8px; }
    .sp-contact-desc { font-size: 13px; color: #64748b; line-height: 1.6; margin-bottom: 16px; }
    .sp-contact-value {
        display: inline-block;
        padding: 8px 20px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s;
    }

    /* Ticket Form */
    .sp-ticket-form {
        background: white;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        padding: 40px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.04);
    }
    .sp-form-group { margin-bottom: 20px; }
    .sp-form-label {
        display: block;
        font-weight: 500;
        font-size: 14px;
        color: #334155;
        margin-bottom: 8px;
    }
    .sp-form-input, .sp-form-select, .sp-form-textarea {
        width: 100%;
        padding: 12px 16px;
        border-radius: 12px;
        border: 1.5px solid #e2e8f0;
        background: #f8fafc;
        font-size: 14px;
        color: #1e293b;
        transition: all 0.3s;
        outline: none;
        font-family: inherit;
    }
    .sp-form-input:focus, .sp-form-select:focus, .sp-form-textarea:focus {
        border-color: #6366f1;
        background: white;
        box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
    }
    .sp-form-textarea { resize: vertical; min-height: 120px; }
    .sp-form-submit {
        width: 100%;
        padding: 14px;
        border-radius: 12px;
        border: none;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: white;
        font-weight: 600;
        font-size: 15px;
        cursor: pointer;
        transition: all 0.3s;
        font-family: inherit;
    }
    .sp-form-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(99,102,241,0.35);
    }
    .sp-form-submit:active { transform: translateY(0); }

    /* Guide Steps */
    .sp-guide-step {
        display: flex;
        align-items: flex-start;
        gap: 20px;
        padding: 24px;
        background: white;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        transition: all 0.3s;
    }
    .sp-guide-step:hover { border-color: #c7d2fe; box-shadow: 0 8px 20px rgba(0,0,0,0.04); }
    .sp-guide-num {
        width: 40px; height: 40px;
        border-radius: 12px;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: white;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700;
        font-size: 16px;
        flex-shrink: 0;
    }
    .sp-guide-step h4 { font-weight: 600; font-size: 15px; color: #1e293b; margin-bottom: 6px; }
    .sp-guide-step p { font-size: 13px; color: #64748b; line-height: 1.6; }

    /* Section Titles */
    .sp-section-title {
        font-size: 28px;
        font-weight: 700;
        color: #1e293b;
        text-align: center;
        margin-bottom: 8px;
    }
    .sp-section-sub {
        font-size: 15px;
        color: #64748b;
        text-align: center;
        margin-bottom: 40px;
    }

    /* Stats */
    .sp-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }
    .sp-stat {
        text-align: center;
        padding: 24px 16px;
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(8px);
        border-radius: 16px;
        border: 1px solid rgba(255,255,255,0.15);
    }
    .sp-stat-num { font-size: 32px; font-weight: 800; color: white; line-height: 1; margin-bottom: 6px; }
    .sp-stat-label { font-size: 13px; color: rgba(255,255,255,0.7); }

    /* Tabs */
    .sp-tabs {
        display: flex;
        gap: 6px;
        background: #f1f5f9;
        padding: 5px;
        border-radius: 14px;
        margin-bottom: 32px;
        justify-content: center;
        flex-wrap: wrap;
    }
    .sp-tab {
        padding: 10px 20px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 500;
        color: #64748b;
        cursor: pointer;
        transition: all 0.3s;
        border: none;
        background: transparent;
        font-family: inherit;
    }
    .sp-tab:hover { color: #1e293b; }
    .sp-tab.active { background: white; color: #6366f1; box-shadow: 0 2px 8px rgba(0,0,0,0.06); font-weight: 600; }

    .sp-faq-group { display: none; }
    .sp-faq-group.active { display: block; }

    /* Toast */
    .sp-toast {
        position: fixed;
        bottom: 40px;
        right: 40px;
        padding: 16px 28px;
        border-radius: 14px;
        background: #059669;
        color: white;
        font-weight: 500;
        font-size: 14px;
        z-index: 10000;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 10px 30px rgba(5,150,105,0.3);
        transform: translateY(100px);
        opacity: 0;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .sp-toast.show { transform: translateY(0); opacity: 1; }

    @media (max-width: 768px) {
        .support-hero { padding: 120px 0 60px; }
        .sp-stats { grid-template-columns: repeat(2, 1fr); }
        .sp-section-title { font-size: 22px; }
        .sp-ticket-form { padding: 24px; }
        .sp-tabs { gap: 4px; }
        .sp-tab { padding: 8px 14px; font-size: 13px; }
    }
</style>
@endpush

@section('content')

<!-- Hero Section -->
<section class="support-hero">
    <div class="hero-content container mx-auto px-4 text-center">
        <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur rounded-full text-white/80 text-sm mb-6">
            <span class="material-icons-round" style="font-size:18px">headset_mic</span>
            Trung tâm hỗ trợ GameTech
        </div>
        <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">Chúng tôi có thể giúp gì cho bạn?</h1>
        <p class="text-white/70 text-lg mb-8 max-w-xl mx-auto">Tìm câu trả lời nhanh chóng hoặc liên hệ đội ngũ hỗ trợ 24/7 của chúng tôi</p>
        

        <div class="sp-stats mt-12 max-w-3xl mx-auto">
            <div class="sp-stat">
                <div class="sp-stat-num">24/7</div>
                <div class="sp-stat-label">Hỗ trợ liên tục</div>
            </div>
            <div class="sp-stat">
                <div class="sp-stat-num">&lt;5p</div>
                <div class="sp-stat-label">Phản hồi nhanh</div>
            </div>
            <div class="sp-stat">
                <div class="sp-stat-num">99%</div>
                <div class="sp-stat-label">Hài lòng</div>
            </div>
            <div class="sp-stat">
                <div class="sp-stat-num">50K+</div>
                <div class="sp-stat-label">Yêu cầu đã xử lý</div>
            </div>
        </div>
    </div>
</section>

<!-- Category Cards -->
<section class="container mx-auto px-4 mb-16">
    <div class="sp-categories">
        <div class="sp-cat-card" style="--cat-color:#6366f1" onclick="scrollToSection('faq-section')">
            <div class="sp-cat-icon" style="background:#EEF2FF;color:#6366f1">
                <span class="material-icons-round">help_outline</span>
            </div>
            <div class="sp-cat-title">Câu hỏi thường gặp</div>
            <div class="sp-cat-desc">Tìm câu trả lời nhanh cho các thắc mắc phổ biến</div>
        </div>
        <div class="sp-cat-card" style="--cat-color:#10b981" onclick="scrollToSection('guide-section')">
            <div class="sp-cat-icon" style="background:#ECFDF5;color:#10b981">
                <span class="material-icons-round">menu_book</span>
            </div>
            <div class="sp-cat-title">Hướng dẫn sử dụng</div>
            <div class="sp-cat-desc">Các bước mua game, nạp tiền, quản lý tài khoản</div>
        </div>
        <div class="sp-cat-card" style="--cat-color:#f59e0b" onclick="scrollToSection('ticket-section')">
            <div class="sp-cat-icon" style="background:#FFFBEB;color:#f59e0b">
                <span class="material-icons-round">confirmation_number</span>
            </div>
            <div class="sp-cat-title">Gửi yêu cầu hỗ trợ</div>
            <div class="sp-cat-desc">Tạo ticket để được đội ngũ hỗ trợ giải quyết</div>
        </div>
        <div class="sp-cat-card" style="--cat-color:#ec4899" onclick="scrollToSection('contact-section')">
            <div class="sp-cat-icon" style="background:#FDF2F8;color:#ec4899">
                <span class="material-icons-round">support_agent</span>
            </div>
            <div class="sp-cat-title">Liên hệ trực tiếp</div>
            <div class="sp-cat-desc">Hotline, email và các kênh liên hệ nhanh</div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="container mx-auto px-4 mb-20" id="faq-section">
    <h2 class="sp-section-title">Câu hỏi thường gặp</h2>
    <p class="sp-section-sub">Tìm câu trả lời nhanh cho các thắc mắc phổ biến nhất</p>

    <div class="max-w-3xl mx-auto">
        <div class="sp-tabs">
            <button class="sp-tab active" data-tab="general">Chung</button>
            <button class="sp-tab" data-tab="account">Tài khoản</button>
            <button class="sp-tab" data-tab="payment">Thanh toán</button>
            <button class="sp-tab" data-tab="order">Đơn hàng</button>
            <button class="sp-tab" data-tab="refund">Hoàn tiền</button>
        </div>

        <!-- General -->
        <div class="sp-faq-group active" data-group="general">
            <div class="sp-faq-item">
                <div class="sp-faq-q" onclick="toggleFaq(this)">
                    <span>GameTech là gì?</span>
                    <div class="sp-faq-icon"><span class="material-icons-round" style="font-size:18px">expand_more</span></div>
                </div>
                <div class="sp-faq-a"><div class="sp-faq-a-inner">GameTech là nền tảng mua bán tài khoản game trực tuyến uy tín hàng đầu Việt Nam. Chúng tôi cung cấp đa dạng các tài khoản game offline và online với giá cả cạnh tranh, bảo hành uy tín và giao dịch an toàn 100%.</div></div>
            </div>
            <div class="sp-faq-item">
                <div class="sp-faq-q" onclick="toggleFaq(this)">
                    <span>Mua game tại GameTech có an toàn không?</span>
                    <div class="sp-faq-icon"><span class="material-icons-round" style="font-size:18px">expand_more</span></div>
                </div>
                <div class="sp-faq-a"><div class="sp-faq-a-inner">Hoàn toàn an toàn! GameTech cam kết bảo mật thông tin khách hàng theo tiêu chuẩn quốc tế. Mọi giao dịch đều được mã hóa SSL và thông tin tài khoản game được bảo vệ nghiêm ngặt. Ngoài ra, chúng tôi có chính sách hoàn tiền rõ ràng nếu có bất kỳ vấn đề nào.</div></div>
            </div>
            <div class="sp-faq-item">
                <div class="sp-faq-q" onclick="toggleFaq(this)">
                    <span>Tôi cần hỗ trợ kĩ thuật, liên hệ như thế nào?</span>
                    <div class="sp-faq-icon"><span class="material-icons-round" style="font-size:18px">expand_more</span></div>
                </div>
                <div class="sp-faq-a"><div class="sp-faq-a-inner">Bạn có thể liên hệ đội ngũ hỗ trợ qua nhiều kênh: Hotline 1900 1234 (24/7), Email support@GameTech.vn, hoặc gửi ticket trực tiếp trên trang này. Chúng tôi cam kết phản hồi trong vòng 5 phút.</div></div>
            </div>
        </div>

        <!-- Account -->
        <div class="sp-faq-group" data-group="account">
            <div class="sp-faq-item">
                <div class="sp-faq-q" onclick="toggleFaq(this)">
                    <span>Làm thế nào để đăng ký tài khoản?</span>
                    <div class="sp-faq-icon"><span class="material-icons-round" style="font-size:18px">expand_more</span></div>
                </div>
                <div class="sp-faq-a"><div class="sp-faq-a-inner">Bạn có thể đăng ký bằng email, hoặc đăng nhập nhanh qua Google/Facebook. Truy cập trang Đăng ký, điền thông tin và xác nhận email là hoàn tất. Toàn bộ quá trình chỉ mất dưới 1 phút.</div></div>
            </div>
            <div class="sp-faq-item">
                <div class="sp-faq-q" onclick="toggleFaq(this)">
                    <span>Tôi quên mật khẩu, phải làm sao?</span>
                    <div class="sp-faq-icon"><span class="material-icons-round" style="font-size:18px">expand_more</span></div>
                </div>
                <div class="sp-faq-a"><div class="sp-faq-a-inner">Truy cập trang Đăng nhập → nhấn "Quên mật khẩu" → nhập email đã đăng ký → kiểm tra hộp thư để nhận link đặt lại mật khẩu. Link có hiệu lực trong 60 phút.</div></div>
            </div>
            <div class="sp-faq-item">
                <div class="sp-faq-q" onclick="toggleFaq(this)">
                    <span>Làm sao để thay đổi thông tin cá nhân?</span>
                    <div class="sp-faq-icon"><span class="material-icons-round" style="font-size:18px">expand_more</span></div>
                </div>
                <div class="sp-faq-a"><div class="sp-faq-a-inner">Truy cập trang Hồ sơ cá nhân → nhấn "Chỉnh sửa" → cập nhật thông tin như tên, ảnh đại diện, ảnh bìa. Sau đó nhấn "Lưu" để xác nhận thay đổi.</div></div>
            </div>
        </div>

        <!-- Payment -->
        <div class="sp-faq-group" data-group="payment">
            <div class="sp-faq-item">
                <div class="sp-faq-q" onclick="toggleFaq(this)">
                    <span>GameTech hỗ trợ những phương thức thanh toán nào?</span>
                    <div class="sp-faq-icon"><span class="material-icons-round" style="font-size:18px">expand_more</span></div>
                </div>
                <div class="sp-faq-a"><div class="sp-faq-a-inner">Chúng tôi hỗ trợ thanh toán qua ví GameTech (nạp qua VNPay, chuyển khoản ngân hàng), thẻ Visa/Mastercard, và ví MoMo. Bạn cần nạp tiền vào ví trước khi mua game.</div></div>
            </div>
            <div class="sp-faq-item">
                <div class="sp-faq-q" onclick="toggleFaq(this)">
                    <span>Nạp tiền vào ví như thế nào?</span>
                    <div class="sp-faq-icon"><span class="material-icons-round" style="font-size:18px">expand_more</span></div>
                </div>
                <div class="sp-faq-a"><div class="sp-faq-a-inner">Truy cập trang Ví → chọn "Nạp tiền" → nhập số tiền → chọn phương thức thanh toán (VNPay) → hoàn tất thanh toán. Tiền sẽ được cộng vào ví ngay lập tức sau khi giao dịch thành công.</div></div>
            </div>
            <div class="sp-faq-item">
                <div class="sp-faq-q" onclick="toggleFaq(this)">
                    <span>Nạp tiền bao lâu thì có?</span>
                    <div class="sp-faq-icon"><span class="material-icons-round" style="font-size:18px">expand_more</span></div>
                </div>
                <div class="sp-faq-a"><div class="sp-faq-a-inner">Nạp qua VNPay: Ngay lập tức (tự động). Nạp qua chuyển khoản ngân hàng: 5-15 phút trong giờ hành chính, tối đa 4 giờ ngoài giờ hành chính. Nếu quá thời gian, vui lòng liên hệ hotline.</div></div>
            </div>
        </div>

        <!-- Order -->
        <div class="sp-faq-group" data-group="order">
            <div class="sp-faq-item">
                <div class="sp-faq-q" onclick="toggleFaq(this)">
                    <span>Sau khi mua game, tôi nhận tài khoản ở đâu?</span>
                    <div class="sp-faq-icon"><span class="material-icons-round" style="font-size:18px">expand_more</span></div>
                </div>
                <div class="sp-faq-a"><div class="sp-faq-a-inner">Sau khi thanh toán thành công, thông tin tài khoản game sẽ hiển thị ngay trong phần "Đơn hàng" của bạn. Bạn có thể xem username, mật khẩu, email và các thông tin khác tại đó.</div></div>
            </div>
            <div class="sp-faq-item">
                <div class="sp-faq-q" onclick="toggleFaq(this)">
                    <span>Tài khoản game mua về bị lỗi thì sao?</span>
                    <div class="sp-faq-icon"><span class="material-icons-round" style="font-size:18px">expand_more</span></div>
                </div>
                <div class="sp-faq-a"><div class="sp-faq-a-inner">Nếu tài khoản game bị lỗi đăng nhập trong vòng 24h kể từ khi mua, hãy liên hệ ngay bộ phận hỗ trợ. Chúng tôi sẽ cung cấp tài khoản thay thế hoặc hoàn tiền 100%.</div></div>
            </div>
            <div class="sp-faq-item">
                <div class="sp-faq-q" onclick="toggleFaq(this)">
                    <span>Có thể xem lại lịch sử đơn hàng không?</span>
                    <div class="sp-faq-icon"><span class="material-icons-round" style="font-size:18px">expand_more</span></div>
                </div>
                <div class="sp-faq-a"><div class="sp-faq-a-inner">Có! Truy cập trang "Đơn hàng" để xem toàn bộ lịch sử mua hàng, bao gồm thông tin tài khoản game, thời gian mua, số tiền và trạng thái đơn hàng.</div></div>
            </div>
        </div>

        <!-- Refund -->
        <div class="sp-faq-group" data-group="refund">
            <div class="sp-faq-item">
                <div class="sp-faq-q" onclick="toggleFaq(this)">
                    <span>Chính sách hoàn tiền như thế nào?</span>
                    <div class="sp-faq-icon"><span class="material-icons-round" style="font-size:18px">expand_more</span></div>
                </div>
                <div class="sp-faq-a"><div class="sp-faq-a-inner">GameTech hoàn tiền 100% trong các trường hợp: tài khoản game bị lỗi không đăng nhập được (trong 24h), sản phẩm không đúng mô tả, hoặc lỗi hệ thống gây ra giao dịch sai. Tiền hoàn sẽ được chuyển về ví GameTech trong 1-3 ngày làm việc.</div></div>
            </div>
            <div class="sp-faq-item">
                <div class="sp-faq-q" onclick="toggleFaq(this)">
                    <span>Thời gian xử lý hoàn tiền là bao lâu?</span>
                    <div class="sp-faq-icon"><span class="material-icons-round" style="font-size:18px">expand_more</span></div>
                </div>
                <div class="sp-faq-a"><div class="sp-faq-a-inner">Hoàn về ví GameTech: 1-3 ngày làm việc. Hoàn về tài khoản ngân hàng: 5-7 ngày làm việc. Bạn sẽ nhận thông báo qua email khi hoàn tiền thành công.</div></div>
            </div>
            <div class="sp-faq-item">
                <div class="sp-faq-q" onclick="toggleFaq(this)">
                    <span>Trường hợp nào không được hoàn tiền?</span>
                    <div class="sp-faq-icon"><span class="material-icons-round" style="font-size:18px">expand_more</span></div>
                </div>
                <div class="sp-faq-a"><div class="sp-faq-a-inner">Không hoàn tiền khi: tài khoản đã sử dụng quá 24h, khách hàng tự thay đổi thông tin tài khoản game dẫn đến lỗi, hoặc vi phạm điều khoản dịch vụ.</div></div>
            </div>
        </div>
    </div>
</section>

<!-- Guide Section -->
<section class="container mx-auto px-4 mb-20" id="guide-section">
    <h2 class="sp-section-title">Hướng dẫn sử dụng</h2>
    <p class="sp-section-sub">Các bước đơn giản để bắt đầu trải nghiệm GameTech</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 max-w-4xl mx-auto">
        <div class="sp-guide-step">
            <div class="sp-guide-num">1</div>
            <div>
                <h4>Đăng ký tài khoản</h4>
                <p>Tạo tài khoản miễn phí bằng email hoặc đăng nhập nhanh qua Google, Facebook chỉ trong vài giây.</p>
            </div>
        </div>
        <div class="sp-guide-step">
            <div class="sp-guide-num">2</div>
            <div>
                <h4>Nạp tiền vào ví</h4>
                <p>Truy cập trang Ví, chọn nạp tiền qua VNPay hoặc chuyển khoản ngân hàng với nhiều mệnh giá linh hoạt.</p>
            </div>
        </div>
        <div class="sp-guide-step">
            <div class="sp-guide-num">3</div>
            <div>
                <h4>Chọn game yêu thích</h4>
                <p>Duyệt cửa hàng, lọc theo thể loại, xem đánh giá từ cộng đồng và chọn tài khoản game phù hợp.</p>
            </div>
        </div>
        <div class="sp-guide-step">
            <div class="sp-guide-num">4</div>
            <div>
                <h4>Thanh toán & nhận game</h4>
                <p>Thêm vào giỏ hàng, thanh toán bằng ví. Thông tin tài khoản game hiển thị ngay trong đơn hàng.</p>
            </div>
        </div>
        <div class="sp-guide-step">
            <div class="sp-guide-num">5</div>
            <div>
                <h4>Kết bạn & nhắn tin</h4>
                <p>Kết nối với cộng đồng game thủ, kết bạn, nhắn tin và chia sẻ trải nghiệm game cùng nhau.</p>
            </div>
        </div>
        <div class="sp-guide-step">
            <div class="sp-guide-num">6</div>
            <div>
                <h4>Đánh giá & góp ý</h4>
                <p>Sau khi trải nghiệm, hãy để lại đánh giá giúp cộng đồng và giúp chúng tôi phục vụ tốt hơn.</p>
            </div>
        </div>
    </div>
</section>

<!-- Ticket Section -->
<section class="container mx-auto px-4 mb-20" id="ticket-section">
    <h2 class="sp-section-title">Gửi yêu cầu hỗ trợ</h2>
    <p class="sp-section-sub">Mô tả vấn đề của bạn và chúng tôi sẽ phản hồi sớm nhất có thể</p>

    <div class="max-w-2xl mx-auto">
        <div class="sp-ticket-form">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="sp-form-group">
                    <label class="sp-form-label">Họ và tên <span style="color:#ef4444">*</span></label>
                    <input type="text" class="sp-form-input" id="tk-name" placeholder="Nhập họ tên">
                </div>
                <div class="sp-form-group">
                    <label class="sp-form-label">Email <span style="color:#ef4444">*</span></label>
                    <input type="email" class="sp-form-input" id="tk-email" placeholder="your@email.com">
                </div>
            </div>
            <div class="sp-form-group">
                <label class="sp-form-label">Danh mục <span style="color:#ef4444">*</span></label>
                <select class="sp-form-select" id="tk-category">
                    <option value="">-- Chọn danh mục --</option>
                    <option value="account">Vấn đề tài khoản</option>
                    <option value="payment">Thanh toán & nạp tiền</option>
                    <option value="order">Đơn hàng & sản phẩm</option>
                    <option value="refund">Hoàn tiền</option>
                    <option value="technical">Lỗi kĩ thuật</option>
                    <option value="other">Khác</option>
                </select>
            </div>
            <div class="sp-form-group">
                <label class="sp-form-label">Tiêu đề <span style="color:#ef4444">*</span></label>
                <input type="text" class="sp-form-input" id="tk-subject" placeholder="Tóm tắt vấn đề của bạn">
            </div>
            <div class="sp-form-group">
                <label class="sp-form-label">Mô tả chi tiết <span style="color:#ef4444">*</span></label>
                <textarea class="sp-form-textarea" id="tk-message" placeholder="Mô tả chi tiết vấn đề bạn đang gặp phải..."></textarea>
            </div>
            <div class="sp-form-group">
                <label class="sp-form-label">Mã đơn hàng (nếu có)</label>
                <input type="text" class="sp-form-input" id="tk-order" placeholder="VD: ORD-20250212-XXXX">
            </div>
            <button class="sp-form-submit" onclick="submitTicket()">
                <span class="material-icons-round" style="font-size:18px;vertical-align:middle;margin-right:8px">send</span>
                Gửi yêu cầu hỗ trợ
            </button>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="container mx-auto px-4 mb-20" id="contact-section">
    <h2 class="sp-section-title">Liên hệ trực tiếp</h2>
    <p class="sp-section-sub">Chọn kênh liên hệ phù hợp nhất với bạn</p>

    <div class="sp-contact-grid max-w-4xl mx-auto">
        <div class="sp-contact-card">
            <div class="sp-contact-icon" style="background:#EEF2FF;color:#6366f1">
                <span class="material-icons-round">call</span>
            </div>
            <div class="sp-contact-title">Hotline 24/7</div>
            <div class="sp-contact-desc">Gọi ngay để được tư vấn viên hỗ trợ trực tiếp, nhanh chóng nhất</div>
            <a href="tel:19001234" class="sp-contact-value" style="background:#EEF2FF;color:#6366f1">1900 1234</a>
        </div>
        <div class="sp-contact-card">
            <div class="sp-contact-icon" style="background:#ECFDF5;color:#10b981">
                <span class="material-icons-round">email</span>
            </div>
            <div class="sp-contact-title">Email hỗ trợ</div>
            <div class="sp-contact-desc">Gửi email mô tả chi tiết vấn đề, chúng tôi phản hồi trong 2 giờ</div>
            <a href="mailto:support@GameTech.vn" class="sp-contact-value" style="background:#ECFDF5;color:#10b981">support@GameTech.vn</a>
        </div>
        <div class="sp-contact-card">
            <div class="sp-contact-icon" style="background:#FDF2F8;color:#ec4899">
                <span class="material-icons-round">forum</span>
            </div>
            <div class="sp-contact-title">Cộng đồng</div>
            <div class="sp-contact-desc">Tham gia cộng đồng GameTech để trao đổi và nhận hỗ trợ từ mọi người</div>
            <a href="{{ url('/community') }}" class="sp-contact-value" style="background:#FDF2F8;color:#ec4899">Tham gia ngay</a>
        </div>
    </div>
</section>

<!-- Toast -->
<div class="sp-toast" id="sp-toast">
    <span class="material-icons-round" style="font-size:20px">check_circle</span>
    <span id="sp-toast-msg"></span>
</div>

@endsection

@push('scripts')
<script>
    // Scroll to section
    function scrollToSection(id) {
        const el = document.getElementById(id);
        if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // Toggle FAQ
    function toggleFaq(el) {
        const item = el.closest('.sp-faq-item');
        const isActive = item.classList.contains('active');
        // Close all in same group
        item.closest('.sp-faq-group').querySelectorAll('.sp-faq-item').forEach(i => i.classList.remove('active'));
        if (!isActive) item.classList.add('active');
    }

    // FAQ Tabs
    document.querySelectorAll('.sp-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.sp-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            const group = this.dataset.tab;
            document.querySelectorAll('.sp-faq-group').forEach(g => g.classList.remove('active'));
            document.querySelector(`.sp-faq-group[data-group="${group}"]`).classList.add('active');
        });
    });



    // Submit ticket
    async function submitTicket() {
        const name = document.getElementById('tk-name').value.trim();
        const email = document.getElementById('tk-email').value.trim();
        const category = document.getElementById('tk-category').value;
        const subject = document.getElementById('tk-subject').value.trim();
        const message = document.getElementById('tk-message').value.trim();

        if (!name || !email || !category || !subject || !message) {
            showToast('Vui lòng điền đầy đủ các trường bắt buộc (*)', 'error');
            return;
        }
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showToast('Vui lòng nhập email hợp lệ', 'error');
            return;
        }

        // Real API submit
        const btn = document.querySelector('.sp-form-submit');
        btn.disabled = true;
        btn.innerHTML = '<span class="material-icons-round" style="font-size:18px;vertical-align:middle;margin-right:8px;animation:spin 1s linear infinite">autorenew</span> Đang gửi...';

        try {
            const headers = { 'Content-Type': 'application/json', 'Accept': 'application/json' };
            const token = localStorage.getItem('auth_token');
            if (token) headers['Authorization'] = 'Bearer ' + token;

            const res = await fetch('{{ url("/api/support-tickets") }}', {
                method: 'POST',
                headers,
                body: JSON.stringify({
                    name, email, category, subject, message,
                    order_code: document.getElementById('tk-order').value.trim() || null
                })
            });
            const data = await res.json();

            if (res.ok && data.success) {
                showToast(`Gửi thành công! Mã ticket: ${data.data.ticket_code}`);
                btn.innerHTML = '<span class="material-icons-round" style="font-size:18px;vertical-align:middle;margin-right:8px">check_circle</span> Đã gửi thành công!';
                // Reset form
                document.getElementById('tk-name').value = '';
                document.getElementById('tk-email').value = '';
                document.getElementById('tk-category').value = '';
                document.getElementById('tk-subject').value = '';
                document.getElementById('tk-message').value = '';
                document.getElementById('tk-order').value = '';
                
                // Auto open messenger to show admin's message (if user is logged in)
                if (token && typeof gToggleConv === 'function') {
                    setTimeout(() => {
                        // Open messenger panel
                        gToggleConv();
                        
                        // Wait for conversation list to load, then auto-select admin chat
                        setTimeout(() => {
                            // Find the first admin in chat list (they just sent a message)
                            const adminItem = document.querySelector('.zalo-conv-item.unread') || document.querySelector('.zalo-conv-item');
                            if (adminItem) {
                                adminItem.click();
                            }
                        }, 800);
                    }, 500);
                }
                
                setTimeout(() => {
                    btn.innerHTML = '<span class="material-icons-round" style="font-size:18px;vertical-align:middle;margin-right:8px">send</span> Gửi yêu cầu hỗ trợ';
                    btn.disabled = false;
                }, 3000);
            } else {
                const errMsg = data.message || Object.values(data.errors || {}).flat().join(', ') || 'Lỗi gửi yêu cầu';
                showToast(errMsg, 'error');
                btn.innerHTML = '<span class="material-icons-round" style="font-size:18px;vertical-align:middle;margin-right:8px">send</span> Gửi yêu cầu hỗ trợ';
                btn.disabled = false;
            }
        } catch (err) {
            showToast('Lỗi kết nối. Vui lòng thử lại.', 'error');
            btn.innerHTML = '<span class="material-icons-round" style="font-size:18px;vertical-align:middle;margin-right:8px">send</span> Gửi yêu cầu hỗ trợ';
            btn.disabled = false;
        }
    }

    // Toast
    function showToast(msg, type) {
        const toast = document.getElementById('sp-toast');
        const msgEl = document.getElementById('sp-toast-msg');
        msgEl.textContent = msg;
        toast.style.background = type === 'error' ? '#dc2626' : '#059669';
        toast.querySelector('.material-icons-round').textContent = type === 'error' ? 'error' : 'check_circle';
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 4000);
    }

    // Auto-fill user info if logged in
    const token = localStorage.getItem('auth_token');
    if (token) {
        fetch('{{ url("/") }}/api/user', {
            headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(d => {
            if (d.id) {
                document.getElementById('tk-name').value = d.name || '';
                document.getElementById('tk-email').value = d.email || '';
            }
        }).catch(() => {});
    }
</script>
@endpush
