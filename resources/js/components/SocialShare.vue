<template>
    <div class="flex gap-2">
      <a
        :href="`https://www.facebook.com/sharer/sharer.php?u=${shareUrl}`"
        target="_blank"
        rel="noopener noreferrer"
        style="background-color:#0866ff;color: #fff;padding: 1px 10px;border-radius: 5px;"
      >
        <i class="fab fa-facebook-f"></i> 
      </a>
  
      <a
        :href="`https://twitter.com/intent/tweet?url=${shareUrl}&text=${shareText}`"
        target="_blank"
        rel="noopener noreferrer"
        style="background-color:#1d9bf0;color: #fff;padding: 1px 10px;border-radius: 5px;"
      >
        <i class="fab fa-twitter"></i>
      </a>
  
      <a
        :href="`https://www.linkedin.com/sharing/share-offsite/?url=${shareUrl}`"
        target="_blank"
        rel="noopener noreferrer"
        style="background-color:#3F51B5;color: #fff;padding: 1px 10px;border-radius: 5px;"
      >
        <i class="fab fa-linkedin-in"></i>
      </a>
      <a
        :href="whatsappShareLink"
        target="_blank"
        rel="noopener noreferrer"
        style="background-color:#25D366;color: #fff;padding: 1px 10px;border-radius: 5px;"
      >
        <i class="fab fa-whatsapp"></i>
      </a>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      shareUrl: {
        type: String,
        required: true
      },
      shareText: {
        type: String,
        default: "Check out this blog!"
      }
    },computed: {
      whatsappShareLink() {
        return `https://api.whatsapp.com/send?text=${encodeURIComponent(this.shareText)}%20${this.shareUrl}`;
      }
    }
  }
  </script>
  