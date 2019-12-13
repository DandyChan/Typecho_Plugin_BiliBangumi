<?php
/**
 * bangumi
 * 
 * @package custom
 * 
 * 追番列表
 * 
 * @author 熊猫小A Edited by 飞蚊话
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
?>

<?php $this->need('head.php'); ?>
<?php $this->need('header.php'); ?>

<div id="main" class="flex flex-1">
    <div class="center flex-1">
        <!--post-item start-->
        <div id="post-list">
        <?php if(!$this->have()):?>
            <div class="post-item">
            <div class="post-item-body" style="padding-top:0.001em"><h1 style="text-align:center;margin-top:40px;color:var(--text-color)">糟糕，是 404 的感觉</h1></div>
            </div>
        <?php else:?>
            <div style="animation-delay:0.2s" class="post-item full">
                <?php if($this->fields->type=='1' || !($this->fields->banner && $this->fields->banner!='')): ?>
                <?php elseif($this->fields->banner && $this->fields->banner!='') :?>
                    <a data-fancybox="gallery" href="<?php echo $this->fields->banner; ?>"><img style="max-width:100%;width:100%" src="<?php echo $this->fields->banner; ?>"/></a>
                <?php endif; ?>
                <div class="post-item-body <?php if($this->fields->banner && $this->is('index')) echo 'pull-left'; if($this->is('index')&&($this->fields->indextype=='1')) echo ' featured';?> flex">
                    <article class="yue">
                    <?php if(!($this->fields->type=='1')): ?>
                        <h1 class="post-title"><?php $this->title();?>
                        <?php if($this->user->hasLogin()): ?>
                            <sup><a target="_blank" href="<?php echo $this->options->adminUrl.'write-post.php?cid='.$this->cid;?>" class="footnote-ref"><i class="fa fa-edit"></i></a></sup>
                        <?php endif;?>
                        </h1>
                    <?php endif; ?>
                    <?php if(!$this->fields->type=='1' && ($this->fields->banner && $this->fields->banner!='')): ?>
                        <div class="post-item-header flex align-items-center" style="padding: 0;font-size:14px;overflow:hidden">
                            <span style="white-space:nowrap;text-overflow:ellipsis;overflow:hidden"><b><i class="fa fa-pencil"></i> <?php echo $this->author->screenName; ?></b> • <?php Utils::exportPostMeta($this,$this->fields->type); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php $diff= round((time()- $this->modified) / 3600 / 24); if($diff>=100): ?>
                    <blockquote>本文最后修改于 <?php echo $diff; ?> 天前，部分内容可能已经过时！</blockquote>
                    <?php endif; ?>
                    <?php if($this->fields->showTOC=='1'):?>
                        <?php 
                            $parsed=Utils::parseTOC(Utils::parseAll($this->content));
                            $GLOBALS['TOC_O']=$parsed['toc'];
                            echo $parsed['content']; 
                        ?>
                    <?php else :?>
                        <?php echo Utils::parseAll($this->content); ?>
                        <?php BiliBangumi_Plugin::output(); ?>
                    <?php endif; ?>
                    </article>
                </div>
                <div class="post-item-footer">
                    <?php if(Utils::isPluginAvailable('Like')):?>
                        <span class="like-button"><a href="javascript:;" class="post-like" data-pid="<?php echo $this->cid;?>">
                            <i class="fa fa-heart"></i> LIKE <span class="like-num"><?php Like_Plugin::theLike($link = false,$this);?></span>
                        </a></span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        </div>
        <!--post-item end-->
        <?php $this->need('footer-info.php'); ?>
    </div>
    <!--<?php $this->need('nav-left.php'); ?>-->
    <!--<?php if($this->options->bloglayout!='1'):?>-->
    <!--    <?php $this->need('aside.php'); ?>-->
    <!--<?php endif;?>-->
</div>
<?php $this->need('footer.php'); ?>