{% extends 'base.html.twig' %}

{% block title %}<?= $action_title; ?> - Qalin{% endblock %}

{% block body %}
    <h1><?= $action_title; ?></h1>
    <form data-api="/api/v1/actions/<?= $action_kebab; ?>" data-expect="201">
<?php foreach ($action_parameters as $param): ?>
        <label for="<?= $param['name']; ?>"><?= ucfirst($param['name']); ?></label>
        <input class="u-full-width" type="text" id="<?= $param['name']; ?>" name="<?= $param['name']; ?>" required>
<?php endforeach; ?>
        <button class="button-primary" type="submit"><?= $action_title; ?></button>
    </form>
    <div class="result"></div>
{% endblock %}
