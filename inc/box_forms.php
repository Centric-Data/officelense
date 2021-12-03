<div class="office__box">
  <style scoped>
    .office__box{
      display: grid;
            grid-template-columns: max-content 1fr;
            grid-row-gap: 10px;
            grid-column-gap: 20px;
    }
    p{
      display: contents;
    }
  </style>
  <p>
    <label for="office_phone">Office Phone Number</label>
    <input type="text" name="office_phone" id="office_phone" value="<?php echo esc_attr( get_post_meta( get_the_ID(), 'office_phone', true ) ); ?>">
  </p>
  <p>
    <label for="office_email">Ofice Email</label>
    <input type="text" name="office_email" id="office_email" value="<?php echo esc_attr( get_post_meta( get_the_ID(), 'office_email', true ) ); ?>">
  </p>

</div>
