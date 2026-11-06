  <template>
    <div class="preloader">
        <div class="spinner"></div>
    </div>
  </template>
  
  <script>
  export default {
    name: 'ProductPreloader',
  };
  </script>
  
  <style scoped>
    .tab-content{
      position: relative;
    }
    .preloader {
      position: absolute;
      top: 15%;
      left: 50%;
      transform: translate(-50%, -50%);
      display: flex;
      justify-content: center;
      align-items: center;
      width: 100%;
      height: 100%;
      /* background-color: rgba(255, 255, 255, 0.8); */
      z-index: 10;
  }

  .spinner {
      border: 4px solid #f3f3f3; /* Light gray */
      border-top: 4px solid #3498db; /* Blue color */
      border-radius: 50%;
      width: 40px;
      height: 40px;
      animation: spin 2s linear infinite;
  }

  @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
  }
  </style>