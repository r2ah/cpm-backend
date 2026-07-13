# TODO - Fix POST /api/people (422)

- [ ] Instrumentar `PersonController@store` para loguear `StorePersonRequest` errors cuando ocurra 422 (no requiere cambiar validaciones).
- [ ] Verificar el error exacto en `StorePersonRequest` (probablemente `unique` de `name` o `email`).
- [ ] Ajustar frontend `PersonaForm.astro` (si el payload no coincide) o ajustar reglas en `StorePersonRequest`.
- [ ] Confirmar que `POST /api/people` responde 201 con los datos del formulario.

