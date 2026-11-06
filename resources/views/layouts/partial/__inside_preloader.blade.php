<style>
    #inside-preloader {
        display: none;
        position: absolute;
        top: 55px;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgb(114 114 114 / 80%);
        z-index: 9999 !important;
    }

    #inside-loader {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        border: 6px solid #c6a4ff;
        border-top: 6px solid #7020f2;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 2s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: translate(-50%, -50%) rotate(0deg);
        }
        100% {
            transform: translate(-50%, -50%) rotate(360deg);
        }
    }
</style>
<div id="inside-preloader-wrapper" style="position: relative;text-align: center">
    <div id="inside-preloader">
        <div id="inside-loader"></div>
    </div>
</div>
