{{ Form::model($tag, array('url'=>link::auth($module['rest'].'/update/'.$tag->id),'class'=>'smart-form','id'=>'tag-form','role'=>'form','method'=>'post')) }}


    <div class="row margin-top-10">
        <!-- Form -->
        <section class="col col-6">
            <div class="well">
                <header>Изменить тег:</header>
                <fieldset>

					<section>
						<label class="label">Модуль</label>
						<label class="input"> <i class="icon-append fa fa-list-alt"></i>
							{{ Form::text('module', @$tag->module) }}
						</label>
					</section>

					<section>
						<label class="label">ID объекта</label>
						<label class="input"> <i class="icon-append fa fa-list-alt"></i>
							{{ Form::text('unit_id', @$tag->unit_id) }}
						</label>
					</section>

					<section>
						<label class="label">Тег</label>
						<label class="input"> <i class="icon-append fa fa-list-alt"></i>
							{{ Form::text('tag', @$tag->tag) }}
						</label>
					</section>

                </fieldset>
            </div>
        </section>
        <!-- /Form -->
    </div>

    <section class="col-6">
        <footer>
            <a class="btn btn-default no-margin regular-10 uppercase pull-left btn-spinner" href="{{URL::previous()}}">
                <i class="fa fa-arrow-left hidden"></i> <span class="btn-response-text">Назад</span>
            </a>
            <button type="submit" autocomplete="off" class="btn btn-success no-margin regular-10 uppercase btn-form-submit">
                <i class="fa fa-spinner fa-spin hidden"></i> <span class="btn-response-text">Сохранить</span>
            </button>
        </footer>
    </section>

{{ Form::close() }}
