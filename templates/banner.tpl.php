<style type="text/css">

.beta-tester {
    width: 100%;
    height: auto;
    margin-bottom: 34px;
    position: relative;
    box-sizing: border-box;
}


.beta-tester__banner {
    width: 100%;
    height: auto;
    margin-bottom: 24px;
    background-image: url('/wp-content/plugins/bidorbuystoreintegrator/assets/images/home-screen-picture.png');
    background-color: #555;
    background-size: cover;
    padding: 30px 0px;
}

.beta-tester::before {
    content: '';
    width: 6px;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    background-color: #7FFF00;
}

#poststuff h2.beta-tester__heading {
    display: block;
    width: 70%;
    margin: 0 auto;
    color: #fff;
    font-family: sans-serif;
    text-align: center;
    font-size: 1.5rem;
    font-weight: 500;
}

#poststuff h2.beta-tester__heading span {
    font-size: 2rem;
    color: #50C878;
}

#poststuff h2.beta-tester__heading--dark {
    color: #000;
    width: 90%;
    margin: 0 auto;
    font-family: sans-serif;
    text-align: center;
    font-size: 1.5rem;
    font-weight: 500;
}


h2 {
    font-size: 1.5rem;
}

h2 span {
    font-size: 2rem;
    color: #50C878;
}

.beta-tester__form {
    font-family: sans-serif;
}

.beta-tester__form--hidden {
    margin: 0;
    padding: 0;
}

.beta-tester__form-wrapper {
    position: fixed;
    z-index: 999999999;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    padding: 40px 60px;
    background-color: #fff;
    border: 1px solid #000;
    border-radius: 8px;
    justify-content: space-between;
}

.beta-tester__form-wrap {
width: 100%;
display: flex;
justify-content: space-between;
margin-bottom: 24px;
}

.beta-tester__form-inner-wrapper {
    display: flex;
    justify-content: space-around;
    flex-direction: column;
    flex-wrap: wrap;

}

.beta-tester__input-wrapper:not(:last-of-type) {
    margin-right: 14px;
}

.beta-tester__label {
    display: block;
    margin-bottom: 6px;
    color: #000;
}

.beta-tester__input {
    display: block;
    min-width: 240px;
    border-radius: 4px;
    line-height: 1.8;
    padding: 4px 12px;
    border: 1px solid rgb(90, 90, 90);
    margin-right: 14px;
    margin-bottom: 24px;
}

.beta-tester__submit {
    box-sizing: border-box;
    display: block;
    margin: 0 auto;
    width: 200px;
    height: 40px;
    padding: 6px 14px;
    font-family: sans-serif;
    font-size: 14px;
    text-transform: uppercase;
    text-decoration: none;
    font-weight: bold;
    color: #fff;
    text-align: center;
    border: none;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    background-color: #50C878;
}

.beta-tester__buttons-wrapper {
    padding: 16px 0px;
    display: flex;
    align-items: center;
    flex-direction: column;
}

.beta-tester__link {
    padding: 6px 14px;
    font-family: sans-serif;
    font-size: 14px;
    text-transform: uppercase;
    text-decoration: none;
    font-weight: bold;
    cursor: pointer;
    margin-bottom: 20px;
    min-width: 124px;
    text-align: center;
}

.beta-tester__link:hover {
    text-decoration: none;
    color: inherit;
}

.beta-tester__link:hover {
    color: #fff;
}

.beta-tester__info {
    color: #48BBD5;
}

.beta-tester__info:hover {
    color: #2792aa;
}

.beta-tester__contact {
    color: #48BBD5;
}

.beta-tester__contact:hover {
    color: #2792aa;
}

.beta-tester__dismiss {
    background-color: transparent;
    border: none;
    outline: none;
    color: #48BBD5;
}

.beta-tester__dismiss:hover {
    color: #2792aa;
}

.beta-tester__cancel {
    background: transparent;
    border: none;
    outline: none;
    color: #2792aa;;
    position: absolute;
    top: 14px;
    right: 14px;
    cursor: pointer;
    font-size: 24px;
}


.beta-tester__links-wrapper {
    flex-grow: 1;
    display: flex;
    justify-content: flex-start;
    flex-direction: column;
}

@media (max-width: 984px) {
    .beta-tester__input-wrapper {
        margin-right: 14px;
    }
}
</style>
<div class="beta-tester" id="banner" hidden="true">
            <div class="beta-tester__banner">
                <form id="beta-tester-form--hidden" name="beta-tester-form" 
                method="POST" action="https://formspree.io/f/mlearavn" class="beta-tester__form--hidden">
                    <input type="hidden" name="adminEmail" value="<?php echo get_option('admin_email') ?>">
                    <input type="hidden" name="platform" value="woocommerce">
                    <input type="hidden" name="action" value="I am not interested">
                    <button type="submit"  class="beta-tester__cancel" 
                    onclick="document.cookie='beta_testing_answered_v=true; max-age=1209600'">X</button>
                </form>
                <h2 class="beta-tester__heading" 
                style="display: block; margin: 0 auto 34px; color: #fff; width: 80%; 
                text-align: center; line-height: 2;"
                >Become MySI Beta tester and get <span>3-month free access</span> to multiple 
                market places <br>(Bidorbuy, Google, Takealot, etc),<br> easy category mapping, regular 
                automatic pricing/inventory updates</h2>
                <button id="banner-inner" class="beta-tester__submit">Submit</button>
            </div>
            <div id="beta-tester__form-wrapper" class="beta-tester__form-wrapper" hidden="true">
                <form name="beta-tester-form" id="beta-tester-form" method="POST" 
                action="https://formspree.io/f/mlearavn" class="beta-tester__form">
                    <div class="beta-tester__form-wrap">
                        <div class="beta-tester__form-inner-wrapper">
                            <div class="beta-tester__input-wrapper">
                                <label class="beta-tester__label" for="name">
                                    Name
                                </label>
                                <input required type="text" class="beta-tester__input" name="name" 
                                id="name" placeholder="Enter your name">
                            </div>
                            <div class="beta-tester__input-wrapper">
                                <label class="beta-tester__label" for="email">
                                    Email
                                </label>
                                <input required type="email" class="beta-tester__input" name="email" 
                                id="email" placeholder="Enter your email">
                            </div>
                            <!-- <div class="beta-tester__form-inner-wrapper"> -->
                                <div class="beta-tester__input-wrapper">
                                    <label class="beta-tester__label" for="phone">
                                        Phone
                                    </label>
                                    <input required type="text" class="beta-tester__input" name="phone" 
                                    id="phone" placeholder="Enter your phone">
                                </div>
                                <input type="hidden" class="beta-tester__input" name="products" 
                                id="name" placeholder="Enter number of products">
                                <input type="hidden" name="platform" value="woocommerce">
                                <!-- </div> -->
                        </div>

                    </div>
                    <button type="submit" class="beta-tester__link beta-tester__submit" 
                    onclick="document.cookie='beta_testing_answered_v=true; max-age=5184000'">Send</button>
                </form>
                <div class="beta-tester__buttons-wrapper" id="buttons-wrapper">
                            <a href="https://www.mysi.app/" target="_blank" 
                            class="beta-tester__link beta-tester__info">Learn more</a>
                            <!-- <a href="https://www.mysi.app/contact-us/" target="_blank" 
                            class="beta-tester__link beta-tester__contact">Contact us</a> -->
                        <form id="beta-tester-form--hidden" name="beta-tester-form" method="POST" 
                        action="https://formspree.io/f/mlearavn" class="beta-tester__form--hidden">
                            <input type="hidden" name="adminEmail" value="<?php echo get_option('admin_email') ?>">
                            <input type="hidden" name="platform" value="woocommerce">
                            <input type="hidden" name="action" value="I am not interested">
                            <button class="beta-tester__link beta-tester__dismiss" 
                            onclick="document.cookie='beta_testing_answered_v=true; max-age=1209600'"
                            >I’m not interested</button>
                        </form>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            function getCookie(name) {
                let matches = document.cookie.match(new RegExp(
                "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
                ));
                    return matches ? decodeURIComponent(matches[1]) : undefined;
            };
            const dismissCookie = getCookie('beta_testing_answered_v');
                const banner = document.querySelector('#banner');
                const formWrapper = document.querySelector('#beta-tester__form-wrapper');
                const bannerInner = document.querySelector('#banner-inner');

                const openForm = () => {
                    formWrapper.removeAttribute('hidden');
                    formWrapper.style.display = 'flex';
                };

                bannerInner.addEventListener('click', openForm);

                if (dismissCookie) {
                    banner.setAttribute('hidden', true);
                } else {
                    // banner.removeAttribute('hidden');
                };
            
        </script>