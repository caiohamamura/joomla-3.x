<?php
/**
 * @package     
 * @subpackage  
 * @copyright   
 * @license     
 */

defined('_JEXEC') or die;
?>
<style>
	.pagenav {
		transition: all 0.2s ease-in-out;
	}
	.pagina-transition-enter-active {
		transition: all 0.2s;
	}
	.pagina-transition-enter{
		display: inline-block;
		opacity:0;
	}
	.pagina-transition-enter-to {
		display: inline-block;
		opacity:1;
	}
	.pagina-transition-leave-active{
		transition: all 0.3s ease-in-out;
		opacity: 1;
	}
	.pagina-transition-leave-to {
		opacity:0;
	}
	.lista-chamadas {
		transition: height 0.5s ease-in-out;
		overflow: hidden;
		height: 702px;
	}
	.lista-chamadas>p {
		display: flex;
		flex-flow: row wrap;
	}
	.lista-enter-active {
		display:none;
		transition: all 0.5s ease-in-out;
	}
	.lista-enter {
		display:inline-block;
		opacity:0.4;
		transform:scaleY(0);
	}
	.lista-enter-to {
		display:inline-block;
		opacity:1;
		transform:scaleY(1);
	}
	.lista-leave-active {
		transition: all 0.2s ease-in-out;
	}
	.lista-leave-to {
		opacity: 0.4;
		transform: scaleY(0);
	}
	.nolink {
		color: black;
		cursor: default;
	}
	.nolink:hover {
		color: initial !important;
		background-color: initial !important;
	}
	.nolink:active {
	    background-color: #f5f5f5 !important;
	}
	.nolink:focus {
		background-color: initial !important;
		color: initial;
	}
</style>
<div id="chamadas-sec-tit-topo">
	<div class="lista-chamadas" ref="lista">
		<transition-group name="lista" v-on:after-leave="onEnter" tag="p">
			<div class="module span4" :class="{'no-margin': index % 3 == 0}" v-for="(item, index) in items" :key="item.id">
				<div class="container-imagem" v-if="getImagem(item) != ''">
					<a :href="getLink(item)" class="img-rounded">
						<template v-if="getImagem(item).indexOf('www.youtube') != -1">
							<iframe :src="getImagem(item)" width="230" height="136" :alt="getAlt(item)" frameborder="0" allow="encrypted-media" allowfullscreen>
							</iframe>
						</template>
						<template v-else>
							<img :src="getImagem(item)" width="230" height="136" :alt="getAlt(item)">
							</img>
						</template>
					</a>
				</div>
				<?php if ($params->get('exibir_title')) : ?>
					<<?php echo $params->get('header_tag'); ?>>
						<a :href="getLink(item)">{{getTitle(item)}}</a>
					</<?php echo $params->get('header_tag'); ?>>
				<?php endif; ?>

				<?php if ($params->get('exibir_introtext')) : ?>
					{{item.introtext}}
				<?php endif; ?>
			</div>
		</transition-group>	
	</div>
	<div class="pagination" :class="{hide: params.moduleclass_sfx.indexOf('sem-paginacao') > 0}">
		<p class="counter pull-left">
			Página <transition name="pagina-transition" mode="out-in"><span :key="this.page">{{this.page}}</span></transition> de {{this.page_total}} 
		</p>
		<ul class="pull-right">
			<template>
				<li class="pagination-start"><a title="Início" href="#" v-on:click="gotoPagina($event,1)" class="hasTooltip pagenav" v-bind:class="{ nolink: this.page == 1 }"><<</a></li>
				<li class="pagination-prev"><a title="Ant" href="#" v-on:click="gotoPagina($event,page-1)" class="hasTooltip pagenav" v-bind:class="{ nolink: this.page == 1 }"><</a></li>
			</template>
			<template v-for="pagina in page_range">
				<li><a class="pagenav" v-bind:class="{ nolink: pagina == page }" href="#" v-on:click="gotoPagina($event,pagina)">{{pagina}}</a></li>
			</template>
			<template>
				<li class="pagination-next"><a title="Próximo" href="#" v-on:click="gotoPagina($event,page+1)" class="hasTooltip pagenav" v-bind:class="{ nolink: this.page == this.page_total }">></a></li>
				<li class="pagination-end"><a title="Fim" href="#" v-on:click="gotoPagina($event,page_total)" class="hasTooltip pagenav" v-bind:class="{ nolink: this.page == this.page_total }">>></a></li>
			</template>
		</ul>
	</div>
	<?php if (! empty($link_saiba_mais) ): ?>
		<div class="outstanding-footer">
			<a href="<?php echo $link_saiba_mais; ?>" class="outstanding-link">
				<?php if ($params->get('texto_saiba_mais')): ?>
					<span class="text"><?php echo $params->get('texto_saiba_mais')?></span>
				<?php else: ?>
					<span class="text">saiba mais</span>
				<?php endif;?>
				<span class="icon-box">                                          
				<i class="icon-angle-right icon-light"><span class="hide">&nbsp;</span></i>
				</span>
			</a>	
		</div>
	<?php endif; ?>
</div>
<script>
var chSecundTopo=new Vue({
    el: '#chamadas-sec-tit-topo',
    data: {
		items: <?php echo json_encode($lista_chamadas); ?>,
		page: 0,
		max_pages: 5,
		quantidade: 10,
		page_range: [1,2,3],
		page_total: 10,
		artigos: 10,
		cache_items: {},
		params: <?php echo json_encode($params); ?>,
		firstUpdate: true,
    },
    beforeCreate: function() {
        if ("chSecundTopo" in Object.assign({},history.state)) {
			var chamSecund = document.querySelector("#chamadas-sec-tit-topo");
			if (chamSecund) 
                chamSecund.innerHTML = history.state.chSecundTopo;
            else {
				var myDiv = document.createElement("div");
				myDiv.id = "chamadas-sec-tit-topo";
				myDiv.innerHTML = history.state.chSecundTopo;
				document.querySelector("#content-section > div.row-fluid.module.no-margin.span12.variacao-module-05 > style").insertAdjacentElement("afterend",myDiv);
			}
        } else { 
            var chamadasSecundarias = {"chSecundTopo": document.querySelector("#chamadas-sec-tit-topo").innerHTML};

            history.pushState(Object.assign({}, history.state, chamadasSecundarias),document.title,document.location.href);
        }
    },
	created: function() {
		var vm = this;
		console.log(vm.params);
		var catids = JSON.stringify(vm.params.catid);
		var params = vm.params;
		var destaque = params.destaque == "0" ? "" : `&destaque=${params.destaque}`;
		this.checkSize();
		window.addEventListener('resize', this.checkSize.bind(this));
		fetch(`index.php?api&command=count&catid=${params.catid}${destaque}&somenteImagem=${params.somente_imagem}`).then(function(res) {
			res.json().then(function(data) {
				if (data && data.length > 0) {
					this.artigos = data[0].contagem;
					this.updatePageRange();
					this.gotoPagina(null,1,this.onEnter);
				}
			}.bind(this));
		}.bind(this));
	},
	updated: function() {
		if (this.firstUpdate && this.$refs && this.$refs.lista) {
			this.firstUpdate = false;
			this.$refs.lista.style.height = this.$refs.lista.firstChild.scrollHeight + "px";
		}
	},
    methods: {
		onEnter: function(e){
			if (this.$refs && this.$refs.lista) {
					this.$refs.lista.style.height = this.$refs.lista.firstChild.scrollHeight + "px";
			}
		},
		checkSize: function() {
			var width = (window.innerWidth > 0) ? window.innerWidth : screen.width;
			if (width < 450) {
				this.max_pages = 3;
				this.quantidade = Math.floor(this.params.quantidade / 2);
			}
			else if (width < 768) {
				this.max_pages = 5;
				this.quantidade = Math.floor(this.params.quantidade / 2);
			}
			else {
				this.max_pages = 10;
				this.quantidade = this.params.quantidade;
			}
			if (this.$refs && this.$refs.lista) {
				this.$refs.lista.style.height = this.$refs.lista.firstChild.scrollHeight + "px";
			}
			this.updatePageRange();
		},
		updatePageRange: function() {
			this.page_total = Math.ceil(this.artigos / this.quantidade);
			var pageMin = this.page - (Math.floor(this.max_pages / 2));
			pageMin = pageMin <= 0 ? 1 : pageMin;
			var pageMax = pageMin + (this.max_pages - 1);
			pageMax = pageMax > this.page_total ? this.page_total : pageMax;
			if (pageMax - pageMin < this.max_pages) {
				pageMin = pageMax - (this.max_pages - 1);
				pageMin = pageMin <= 0 ? 1 : pageMin;
			}
			var size = pageMax - pageMin + 1;
			this.page_range = Array.apply(0, {length: size}).map(function(e,i){return i+pageMin});
		},
		getImagem: function(item) {
            var imageSrc = JSON.parse(item['images']).image_intro;
			if (imageSrc == "") {
				imageSrc = "images/artigo-padrao.jpg";
			}
			return imageSrc;
		},
		getAlt: function(item) {
            var imageAlt = JSON.parse(item['images']).image_intro_alt;
			return imageAlt;
		},
        getTitle: function(item) {
            var theTitle = item.title;
            var legend = JSON.parse(item['images'])["image_fulltext_caption"];
            if (legend) {
                theTitle = legend;
                if (!theTitle.match(/^\d{2}\/\d{2}/)) {
                    var date = new Date(item.created_date);
                    theTitle = date.toLocaleDateString().replace(/\/?\d{4}\/?/,"") + " " + theTitle;
                }
            }
            return (theTitle);
		},
		getLink: function(item) {
          var link = "/index.php/component/content/article?id="+item.id;
		  return (link);
		},
		gotoPagina: function(e, pag, func) {
			if (e) e.preventDefault();
			if (pag == this.page || pag == 0 || pag > this.page_total) return false;
			if (pag in this.cache_items) {
				this.items = this.cache_items[pag];
				this.page = pag;
				this.updatePageRange();
				return false;
			}
			this.getList(pag).then(function(data) {
					data = data.map(function(obj, i){obj["index"]=i;return(obj)});
					this.items = data;
					this.cache_items[pag] = data;
					this.page = pag;
					this.updatePageRange();
					if (func) func();
			}.bind(this));
			return false;
		},
		getList: async function(pag) {
			var params = this.params;
			var catIds = encodeURIComponent(JSON.stringify(this.params.catid));
			var destaque = params.destaque == "0" ? "" : `&destaque=${params.destaque}`;
            var url = `index.php?api&command=list&catid=${catIds}${destaque}&nrows=${this.quantidade}&ordem=${params.ordem}&ordem_direction=${params.ordem_direction}&page=${pag}&somenteImagem=${params.somente_imagem}`;
            if (pag > 1) {
            	if (gaUpdate) gaUpdate(url);
 			}
			return (await 
			(await 
			fetch(url)).json());
		}
    }
});
</script>
<?php 
$doc = JFactory::getDocument();
$doc->addScript("https://cdn.jsdelivr.net/npm/vue@2.6.11"); 
?>
