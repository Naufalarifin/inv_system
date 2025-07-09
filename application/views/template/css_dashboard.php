<script src="<?php echo $config['base_url']; ?>css/Chart.js"></script>

<style>

.circle-tile {
    margin-bottom: 15px;
    text-align: center;
}
.circle-tile-heading {
    border: 3px solid rgba(255, 255, 255, 0.3);
    border-radius: 100%;
    color: #FFFFFF;
    height: 80px;
    margin: 0 auto -40px;
    position: relative;
    transition: all 0.3s ease-in-out 0s;
    width: 80px;
    padding:16px 0px 0px 3px;font-size:36px;
}
.circle-tile-heading .fa {
    line-height: 80px;
}
.circle-tile-content {
    padding-top: 50px;
}
.circle-tile-number {
    font-size: 30px;
    font-weight: 700;
    line-height: 1;
    padding: 5px 0 15px;
}
.circle-tile-description {
    font-size:16px;
    font-weight: bold;color:#FFF;
}
.circle-tile-footer {
    background-color: rgba(0, 0, 0, 0.1);
    color: rgba(255, 255, 255, 0.5);
    display: block;
    padding: 5px;
    transition: all 0.3s ease-in-out 0s;
    text-decoration: none;
}
.circle-tile-footer:hover {
    background-color: rgba(0, 0, 0, 0.2);
    color: rgba(255, 255, 255, 0.5);
    text-decoration: none;
}
.tile-img {
    text-shadow: 2px 2px 3px rgba(0, 0, 0, 0.9);
}

.text-faded {
    color: rgba(255, 255, 255, 0.8);
}


.db_sq_icon {
    height:60px;width:60px;font-size:35px;float:left;padding:13px 14px 22px 12px;color:#FFF;
}

.db_sq_content {
    height:60px;width:calc(100% - 60px);padding:2px 20px 10px 10px;color:#FFF;float:left;
}

.db_sq_num {
    font-size: 30px;font-weight: bold;
}

.db_sq_label {
    font-size:18px;margin-top:-12px;
}





</style>
